<?php

namespace App\Services\Payroll;

use App\Models\EmployeeProfile;
use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollCalculationService
{
    public function __construct(
        private readonly PayrollMoneyService $money
    ) {}

    public function calculatePeriod(
        PayrollPeriod $period
    ): PayrollPeriod {
        return DB::transaction(
            function () use (
                $period
            ): PayrollPeriod {
                $period =
                    PayrollPeriod::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $period->id
                        );

                if (
                    ! in_array(
                        $period->status,
                        [
                            'draft',
                            'calculated',
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'Only draft or calculated payroll periods may be recalculated.',
                    ]);
                }

                $employees =
                    EmployeeProfile::query()
                        ->whereIn(
                            'status',
                            [
                                'active',
                                'on_leave',
                            ]
                        )
                        ->with(
                            'activeCompensation'
                        )
                        ->get();

                foreach (
                    $employees as $employee
                ) {
                    $compensation =
                        $employee
                            ->activeCompensation;

                    if (! $compensation) {
                        continue;
                    }

                    $basic =
                        $this->money->toMinor(
                            $compensation
                                ->monthly_basic_salary
                        );

                    $allowance =
                        $this->money->toMinor(
                            $compensation
                                ->fixed_allowance
                        );

                    $deduction =
                        $this->money->toMinor(
                            $compensation
                                ->fixed_deduction
                        );

                    $adjustments =
                        PayrollEntry::query()
                            ->where(
                                'payroll_period_id',
                                $period->id
                            )
                            ->where(
                                'employee_profile_id',
                                $employee->id
                            )
                            ->first()
                            ?->adjustments()
                            ->get()
                            ?? collect();

                    $extraAllowance = 0;
                    $extraDeduction = 0;

                    foreach (
                        $adjustments as $adjustment
                    ) {
                        $minor =
                            $this->money->toMinor(
                                $adjustment
                                    ->amount
                            );

                        if (
                            $adjustment->type
                            === 'allowance'
                        ) {
                            $extraAllowance +=
                                $minor;
                        } else {
                            $extraDeduction +=
                                $minor;
                        }
                    }

                    $allowances =
                        $allowance
                        + $extraAllowance;

                    $deductions =
                        $deduction
                        + $extraDeduction;

                    /*
                     * Overtime payment is intentionally zero in the first
                     * M15 version until an explicit overtime-rate policy
                     * exists.
                     */
                    $overtimeAmount = 0;

                    $gross =
                        $basic
                        + $allowances
                        + $overtimeAmount;

                    $net =
                        max(
                            0,
                            $gross
                            - $deductions
                        );

                    PayrollEntry::updateOrCreate(
                        [
                            'payroll_period_id' => $period->id,

                            'employee_profile_id' => $employee->id,
                        ],
                        [
                            'currency' => $compensation
                                ->currency,

                            'basic_salary' => $this->money
                                ->fromMinor(
                                    $basic
                                ),

                            'allowances' => $this->money
                                ->fromMinor(
                                    $allowances
                                ),

                            'overtime_amount' => '0.00',

                            'gross_pay' => $this->money
                                ->fromMinor(
                                    $gross
                                ),

                            'deductions' => $this->money
                                ->fromMinor(
                                    $deductions
                                ),

                            'net_pay' => $this->money
                                ->fromMinor(
                                    $net
                                ),

                            'calculated_at' => now(),
                        ]
                    );
                }

                $period->update([
                    'status' => 'calculated',

                    'calculated_at' => now(),
                ]);

                return $period->refresh();
            }
        );
    }
}
