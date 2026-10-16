<?php

namespace App\Services\Leave;

use App\Models\EmployeeProfile;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LeaveBalanceService
{
    public function ensure(
        EmployeeProfile $employee,
        LeaveType $leaveType,
        int $year
    ): LeaveBalance {
        return LeaveBalance::firstOrCreate(
            [
                'employee_profile_id' => $employee->id,

                'leave_type_id' => $leaveType->id,

                'year' => $year,
            ],
            [
                'opening_balance' => 0,

                'accrued' => $leaveType
                    ->annual_entitlement,

                'used' => 0,

                'adjusted' => 0,

                'available' => $leaveType
                    ->annual_entitlement,
            ]
        );
    }

    public function consumeApproved(
        LeaveRequest $request
    ): void {
        DB::transaction(
            function () use (
                $request
            ): void {
                $balance =
                    LeaveBalance::query()
                        ->where(
                            'employee_profile_id',
                            $request
                                ->employee_profile_id
                        )
                        ->where(
                            'leave_type_id',
                            $request
                                ->leave_type_id
                        )
                        ->where(
                            'year',
                            $request
                                ->start_date
                                ->year
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                $days =
                    (float) $request->days;

                $available =
                    (float) $balance
                        ->available;

                if (
                    ! $request
                        ->leaveType
                        ->allow_negative_balance
                    && $days > $available
                ) {
                    throw ValidationException::withMessages([
                        'leave' => 'The employee does not have enough leave balance.',
                    ]);
                }

                $balance->update([
                    'used' => round(
                        (float) $balance->used
                        + $days,
                        2
                    ),

                    'available' => round(
                        $available
                        - $days,
                        2
                    ),
                ]);
            }
        );
    }
}
