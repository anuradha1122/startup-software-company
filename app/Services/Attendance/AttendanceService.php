<?php

namespace App\Services\Attendance;

use App\Models\AttendanceRecord;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function clockIn(
        EmployeeProfile $employee
    ): AttendanceRecord {
        return DB::transaction(
            function () use (
                $employee
            ): AttendanceRecord {
                $existing =
                    AttendanceRecord::query()
                        ->where(
                            'employee_profile_id',
                            $employee->id
                        )
                        ->whereDate(
                            'work_date',
                            today()
                        )
                        ->lockForUpdate()
                        ->first();

                if (
                    $existing
                    && $existing->clock_in_at
                ) {
                    throw ValidationException::withMessages([
                        'attendance' => 'You have already clocked in today.',
                    ]);
                }

                if ($existing) {
                    $existing->update([
                        'clock_in_at' => now(),

                        'status' => 'present',

                        'source' => 'self',
                    ]);

                    return $existing->refresh();
                }

                return AttendanceRecord::create([
                    'employee_profile_id' => $employee->id,

                    'work_date' => today()
                        ->toDateString(),

                    'clock_in_at' => now(),

                    'status' => 'present',

                    'source' => 'self',
                ]);
            }
        );
    }

    public function clockOut(
        EmployeeProfile $employee
    ): AttendanceRecord {
        return DB::transaction(
            function () use (
                $employee
            ): AttendanceRecord {
                $attendance =
                    AttendanceRecord::query()
                        ->where(
                            'employee_profile_id',
                            $employee->id
                        )
                        ->whereDate(
                            'work_date',
                            today()
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                if (! $attendance->clock_in_at) {
                    throw ValidationException::withMessages([
                        'attendance' => 'You must clock in before clocking out.',
                    ]);
                }

                if ($attendance->clock_out_at) {
                    throw ValidationException::withMessages([
                        'attendance' => 'You have already clocked out today.',
                    ]);
                }

                $clockOut =
                    now();

                $minutes =
                    $attendance
                        ->clock_in_at
                        ->diffInMinutes(
                            $clockOut
                        );

                $hours =
                    round(
                        $minutes / 60,
                        2
                    );

                $overtime =
                    max(
                        0,
                        round(
                            $hours - 8,
                            2
                        )
                    );

                $attendance->update([
                    'clock_out_at' => $clockOut,

                    'worked_hours' => $hours,

                    'overtime_hours' => $overtime,
                ]);

                return $attendance->refresh();
            }
        );
    }
}
