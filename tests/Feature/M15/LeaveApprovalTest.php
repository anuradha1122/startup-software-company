<?php

use App\Models\EmployeeProfile;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Notifications\LeaveRequestReviewedNotification;
use App\Services\Leave\LeaveRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

it('approves leave and consumes employee balance once', function (): void {
    $employeeUser =
        User::factory()->create();

    $reviewer =
        User::factory()->create();

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

            'allow_negative_balance' => false,

            'active' => true,
        ]);

    $balance =
        LeaveBalance::create([
            'employee_profile_id' => $employee->id,

            'leave_type_id' => $leaveType->id,

            'year' => 2026,

            'opening_balance' => '0.00',

            'accrued' => '14.00',

            'used' => '0.00',

            'adjusted' => '0.00',

            'available' => '14.00',
        ]);

    $leave =
        LeaveRequest::create([
            'leave_number' => 'LV-000001',

            'employee_profile_id' => $employee->id,

            'leave_type_id' => $leaveType->id,

            'start_date' => '2026-10-19',

            'end_date' => '2026-10-20',

            'days' => '2.00',

            'status' => 'submitted',

            'submitted_at' => now(),
        ]);

    $result =
        app(
            LeaveRequestService::class
        )->review(
            $leave,
            $reviewer,
            'approved',
            'Approved.'
        );

    expect(
        $result->status
    )->toBe('approved');

    expect(
        $result->approved_at
    )->not->toBeNull();

    expect(
        (float) $balance
            ->fresh()
            ->used
    )->toBe(2.0);

    expect(
        (float) $balance
            ->fresh()
            ->available
    )->toBe(12.0);

    Notification::assertSentTo(
        $employeeUser,
        LeaveRequestReviewedNotification::class
    );
});
