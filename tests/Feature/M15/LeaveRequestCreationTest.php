<?php

use App\Models\EmployeeProfile;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Notifications\LeaveRequestSubmittedNotification;
use App\Services\Leave\LeaveRequestService;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();

    $this->seed([
        CompanySettingSeeder::class,
        RolePermissionSeeder::class,
    ]);
});

it('submits employee leave request and initializes balance', function (): void {
    $employeeUser =
        User::factory()->create();

    $reviewer =
        User::factory()->create();

    $reviewer->givePermissionTo(
        'leave-requests.review'
    );

    $employee =
        EmployeeProfile::create([
            'employee_number' => 'EMP-000001',

            'user_id' => $employeeUser->id,

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

    $leave =
        app(
            LeaveRequestService::class
        )->submit(
            $employee,
            [
                'leave_type_id' => $leaveType->id,

                'start_date' => '2026-10-19',

                'end_date' => '2026-10-20',

                'reason' => 'Personal leave.',
            ]
        );

    expect(
        $leave
    )->toBeInstanceOf(
        LeaveRequest::class
    );

    expect(
        $leave->leave_number
    )->toStartWith('LV-');

    expect(
        $leave->status
    )->toBe('submitted');

    expect(
        (float) $leave->days
    )->toBe(2.0);

    $this->assertDatabaseHas(
        'leave_balances',
        [
            'employee_profile_id' => $employee->id,

            'leave_type_id' => $leaveType->id,

            'year' => 2026,
        ]
    );

    Notification::assertSentTo(
        $reviewer,
        LeaveRequestSubmittedNotification::class
    );
});
