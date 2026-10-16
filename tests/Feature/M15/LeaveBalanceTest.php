<?php

use App\Models\EmployeeProfile;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\Leave\LeaveBalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('initializes one annual leave balance per employee type and year', function (): void {
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

    $leaveType =
        LeaveType::create([
            'code' => 'ANNUAL',

            'name' => 'Annual Leave',

            'annual_entitlement' => '14.00',

            'paid' => true,

            'active' => true,
        ]);

    $service =
        app(
            LeaveBalanceService::class
        );

    $first =
        $service->ensure(
            $employee,
            $leaveType,
            2026
        );

    $second =
        $service->ensure(
            $employee,
            $leaveType,
            2026
        );

    expect(
        $first->id
    )->toBe(
        $second->id
    );

    expect(
        (float) $first->accrued
    )->toBe(14.0);

    expect(
        (float) $first->available
    )->toBe(14.0);

    expect(
        LeaveBalance::count()
    )->toBe(1);
});
