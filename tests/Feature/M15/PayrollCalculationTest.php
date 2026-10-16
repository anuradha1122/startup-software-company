<?php

use App\Models\EmployeeCompensation;
use App\Models\EmployeeProfile;
use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Services\Payroll\PayrollCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('calculates payroll from active employee compensation', function (): void {
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

    EmployeeCompensation::create([
        'employee_profile_id' => $employee->id,

        'currency' => 'LKR',

        'monthly_basic_salary' => '100000.00',

        'fixed_allowance' => '10000.00',

        'fixed_deduction' => '5000.00',

        'effective_from' => '2026-01-01',

        'active' => true,
    ]);

    $period =
        PayrollPeriod::create([
            'payroll_number' => 'PAY-2026-10',

            'name' => 'October 2026',

            'period_start' => '2026-10-01',

            'period_end' => '2026-10-31',

            'payment_date' => '2026-10-31',

            'status' => 'draft',
        ]);

    app(
        PayrollCalculationService::class
    )->calculatePeriod(
        $period
    );

    $entry =
        PayrollEntry::firstOrFail();

    expect(
        $period
            ->fresh()
            ->status
    )->toBe('calculated');

    expect(
        $entry->currency
    )->toBe('LKR');

    expect(
        $entry->basic_salary
    )->toBe('100000.00');

    expect(
        $entry->allowances
    )->toBe('10000.00');

    expect(
        $entry->deductions
    )->toBe('5000.00');

    expect(
        $entry->gross_pay
    )->toBe('110000.00');

    expect(
        $entry->net_pay
    )->toBe('105000.00');

    expect(
        $entry->overtime_amount
    )->toBe('0.00');
});
