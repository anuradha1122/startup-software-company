<?php

use App\Models\EmployeeProfile;
use App\Models\User;
use App\Services\Attendance\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function (): void {
    Carbon::setTestNow();
});

it('records employee clock in and clock out', function (): void {
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

    Carbon::setTestNow(
        '2026-10-16 08:30:00'
    );

    $service =
        app(
            AttendanceService::class
        );

    $attendance =
        $service->clockIn(
            $employee
        );

    expect(
        $attendance->clock_in_at
    )->not->toBeNull();

    expect(
        $attendance->clock_out_at
    )->toBeNull();

    expect(
        $attendance->status
    )->toBe('present');

    Carbon::setTestNow(
        '2026-10-16 17:00:00'
    );

    $attendance =
        $service->clockOut(
            $employee
        );

    expect(
        $attendance->clock_out_at
    )->not->toBeNull();

    expect(
        (float) $attendance
            ->worked_hours
    )->toBe(8.5);

    expect(
        (float) $attendance
            ->overtime_hours
    )->toBe(0.5);
});
