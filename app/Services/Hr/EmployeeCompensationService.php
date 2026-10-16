<?php

namespace App\Services\Hr;

use App\Models\EmployeeCompensation;
use App\Models\EmployeeProfile;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmployeeCompensationService
{
    public function replaceActive(
        EmployeeProfile $employee,
        array $data,
        User $actor
    ): EmployeeCompensation {
        return DB::transaction(
            function () use (
                $employee,
                $data,
                $actor
            ): EmployeeCompensation {
                $employee =
                    EmployeeProfile::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $employee->id
                        );

                if (
                    $employee->status
                    === 'terminated'
                ) {
                    throw ValidationException::withMessages([
                        'employee' => 'Terminated employees cannot receive a new active compensation record.',
                    ]);
                }

                EmployeeCompensation::query()
                    ->where(
                        'employee_profile_id',
                        $employee->id
                    )
                    ->where(
                        'active',
                        true
                    )
                    ->update([
                        'active' => false,

                        'effective_to' => CarbonImmutable::parse(
                            $data['effective_from']
                        )
                            ->subDay()
                            ->toDateString(),
                    ]);

                return EmployeeCompensation::create([
                    'employee_profile_id' => $employee->id,

                    'currency' => strtoupper(
                        $data['currency']
                    ),

                    'monthly_basic_salary' => $data[
                            'monthly_basic_salary'
                        ],

                    'fixed_allowance' => $data[
                            'fixed_allowance'
                        ] ?? 0,

                    'fixed_deduction' => $data[
                            'fixed_deduction'
                        ] ?? 0,

                    'effective_from' => $data[
                            'effective_from'
                        ],

                    'active' => true,

                    'notes' => $data['notes']
                        ?? null,

                    'created_by' => $actor->id,
                ]);
            }
        );
    }
}
