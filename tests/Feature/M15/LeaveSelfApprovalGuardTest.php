<?php

use App\Models\EmployeeProfile;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\Leave\LeaveRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

it('prevents employee from reviewing own leave request', function (): void {
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

    LeaveBalance::create([
        'employee_profile_id' => $employee->id,

        'leave_type_id' => $leaveType->id,

        'year' => 2026,

        'accrued' => '14.00',

        'available' => '14.00',
    ]);

    $leave =
        LeaveRequest::create([
            'leave_number' => 'LV-000001',

            'employee_profile_id' => $employee->id,

            'leave_type_id' => $leaveType->id,

            'start_date' => '2026-10-19',

            'end_date' => '2026-10-19',

            'days' => '1.00',

            'status' => 'submitted',

            'submitted_at' => now(),
        ]);

    expect(
        fn () => app(
            LeaveRequestService::class
        )->review(
            $leave,
            $user,
            'approved',
            null
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        $leave
            ->fresh()
            ->status
    )->toBe('submitted');
});
