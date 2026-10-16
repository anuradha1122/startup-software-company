<?php

use App\Models\EmployeeProfile;
use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Notifications\PayrollPublishedNotification;
use App\Services\Payroll\PayrollWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

it('publishes approved payroll and notifies employee', function (): void {
    $publisher =
        User::factory()->create();

    $employeeUser =
        User::factory()->create();

    $employee =
        EmployeeProfile::create([
            'employee_number' => 'EMP-000001',

            'user_id' => $employeeUser->id,

            'employment_type' => 'permanent',

            'status' => 'active',

            'hire_date' => '2025-01-01',
        ]);

    $period =
        PayrollPeriod::create([
            'payroll_number' => 'PAY-2026-10',

            'name' => 'October 2026',

            'period_start' => '2026-10-01',

            'period_end' => '2026-10-31',

            'payment_date' => '2026-10-31',

            'status' => 'approved',

            'approved_by' => $publisher->id,

            'approved_at' => now(),
        ]);

    PayrollEntry::create([
        'payroll_period_id' => $period->id,

        'employee_profile_id' => $employee->id,

        'currency' => 'LKR',

        'basic_salary' => '100000.00',

        'allowances' => '10000.00',

        'gross_pay' => '110000.00',

        'deductions' => '5000.00',

        'net_pay' => '105000.00',
    ]);

    $result =
        app(
            PayrollWorkflowService::class
        )->publish(
            $period,
            $publisher
        );

    expect(
        $result->status
    )->toBe('published');

    expect(
        $result->published_by
    )->toBe(
        $publisher->id
    );

    expect(
        $result->published_at
    )->not->toBeNull();

    Notification::assertSentTo(
        $employeeUser,
        PayrollPublishedNotification::class
    );
});

it('does not publish unapproved payroll', function (): void {
    $publisher =
        User::factory()->create();

    $period =
        PayrollPeriod::create([
            'payroll_number' => 'PAY-2026-11',

            'name' => 'November 2026',

            'period_start' => '2026-11-01',

            'period_end' => '2026-11-30',

            'payment_date' => '2026-11-30',

            'status' => 'calculated',
        ]);

    expect(
        fn () => app(
            PayrollWorkflowService::class
        )->publish(
            $period,
            $publisher
        )
    )->toThrow(
        ValidationException::class
    );
});
