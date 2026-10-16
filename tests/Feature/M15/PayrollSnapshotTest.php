<?php

use App\Models\EmployeeCompensation;
use App\Models\EmployeeProfile;
use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Services\Payroll\PayrollCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('preserves payroll salary snapshot after compensation changes', function (): void {
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

    $compensation =
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
        $entry->basic_salary
    )->toBe('100000.00');

    expect(
        $entry->gross_pay
    )->toBe('110000.00');

    expect(
        $entry->net_pay
    )->toBe('105000.00');

    $compensation->update([
        'monthly_basic_salary' => '150000.00',

        'fixed_allowance' => '20000.00',

        'fixed_deduction' => '7500.00',
    ]);

    $entry->refresh();

    /*
     * Payroll entries are historical snapshots.
     * Updating compensation must not silently mutate
     * already calculated payroll.
     */
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
        $entry->net_pay
    )->toBe('105000.00');
});
