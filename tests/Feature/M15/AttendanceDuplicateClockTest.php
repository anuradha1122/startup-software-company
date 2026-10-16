<?php

use App\Models\EmployeeProfile;
use App\Models\User;
use App\Services\Attendance\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

afterEach(function (): void {
    Carbon::setTestNow();
});

it('rejects duplicate employee clock in', function (): void {
    Carbon::setTestNow(
        '2026-10-16 08:00:00'
    );

    $user =
        User::factory()->create();

    $employee =
        EmployeeProfile::create([
            'employee_number' => 'EMP-000001',

            'user_id' => $user->id,

            'employment_type' => 'permanent',

            'status' => 'active',

            'hire_date' => '2025-01-01',
        ]);

    $service =
        app(
            AttendanceService::class
        );

    $service->clockIn(
        $employee
    );

    expect(
        fn () => $service->clockIn(
            $employee
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        $employee
            ->attendanceRecords()
            ->count()
    )->toBe(1);
});

it('rejects duplicate employee clock out', function (): void {
    Carbon::setTestNow(
        '2026-10-16 08:00:00'
    );

    $user =
        User::factory()->create();

    $employee =
        EmployeeProfile::create([
            'employee_number' => 'EMP-000002',

            'user_id' => $user->id,

            'employment_type' => 'permanent',

            'status' => 'active',

            'hire_date' => '2025-01-01',
        ]);

    $service =
        app(
            AttendanceService::class
        );

    $service->clockIn(
        $employee
    );

    Carbon::setTestNow(
        '2026-10-16 17:00:00'
    );

    $service->clockOut(
        $employee
    );

    expect(
        fn () => $service->clockOut(
            $employee
        )
    )->toThrow(
        ValidationException::class
    );
});
