<?php

use App\Models\EmployeeProfile;
use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('prevents employee from downloading another employees payslip', function (): void {
    $userA =
        User::factory()->create();

    $userB =
        User::factory()->create();

    $userA->givePermissionTo(
        'payslips.download-own'
    );

    $employeeA =
        EmployeeProfile::create([
            'employee_number' => 'EMP-A',

            'user_id' => $userA->id,

            'employment_type' => 'permanent',

            'status' => 'active',

            'hire_date' => '2025-01-01',
        ]);

    $employeeB =
        EmployeeProfile::create([
            'employee_number' => 'EMP-B',

            'user_id' => $userB->id,

            'employment_type' => 'permanent',

            'status' => 'active',

            'hire_date' => '2025-01-01',
        ]);

    expect(
        $employeeA->id
    )->not->toBe(
        $employeeB->id
    );

    $period =
        PayrollPeriod::create([
            'payroll_number' => 'PAY-2026-10',

            'name' => 'October 2026',

            'period_start' => '2026-10-01',

            'period_end' => '2026-10-31',

            'payment_date' => '2026-10-31',

            'status' => 'published',

            'published_at' => now(),
        ]);

    $entry =
        PayrollEntry::create([
            'payroll_period_id' => $period->id,

            'employee_profile_id' => $employeeB->id,

            'currency' => 'LKR',

            'basic_salary' => '100000.00',

            'allowances' => '0.00',

            'gross_pay' => '100000.00',

            'deductions' => '0.00',

            'net_pay' => '100000.00',
        ]);

    $this
        ->actingAs($userA)
        ->get(
            "/employee/payslips/{$entry->public_id}/pdf"
        )
        ->assertNotFound();
});
