<?php

use App\Models\EmployeeProfile;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\Leave\LeaveRequestService;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();

    $this->seed([
        CompanySettingSeeder::class,
        RolePermissionSeeder::class,
    ]);
});

it('rejects overlapping active leave request', function (): void {
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

            'annual_entitlement' => '20.00',

            'active' => true,
        ]);

    $service =
        app(
            LeaveRequestService::class
        );

    $service->submit(
        $employee,
        [
            'leave_type_id' => $leaveType->id,

            'start_date' => '2026-10-19',

            'end_date' => '2026-10-21',

            'reason' => 'First request.',
        ]
    );

    expect(
        fn () => $service->submit(
            $employee,
            [
                'leave_type_id' => $leaveType->id,

                'start_date' => '2026-10-20',

                'end_date' => '2026-10-23',

                'reason' => 'Overlapping request.',
            ]
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        $employee
            ->leaveRequests()
            ->count()
    )->toBe(1);
});
