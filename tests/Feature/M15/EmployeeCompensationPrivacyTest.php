<?php

use App\Models\EmployeeCompensation;
use App\Models\EmployeeProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('does not expose employee compensation without compensation permission', function (): void {
    $viewer =
        User::factory()->create();

    $viewer->givePermissionTo(
        'employees.view'
    );

    $employeeUser =
        User::factory()->create();

    $employee =
        EmployeeProfile::create([
            'employee_number' => 'EMP-000001',

            'user_id' => $employeeUser->id,

            'employment_type' => 'permanent',

            'status' => 'active',

            'hire_date' => '2026-01-01',
        ]);

    EmployeeCompensation::create([
        'employee_profile_id' => $employee->id,

        'currency' => 'LKR',

        'monthly_basic_salary' => '123456.78',

        'fixed_allowance' => '10000.00',

        'fixed_deduction' => '2500.00',

        'effective_from' => '2026-01-01',

        'active' => true,
    ]);

    $this
        ->actingAs($viewer)
        ->get(
            "/admin/hr/employees/{$employee->public_id}"
        )
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component(
                    'admin/hr/employees/show'
                )
                ->where(
                    'canViewCompensation',
                    false
                )
                ->missing(
                    'employee.compensation'
                )
        )
        ->assertDontSee(
            '123456.78'
        );
});

it('allows authorized hr user to view compensation', function (): void {
    $viewer =
        User::factory()->create();

    $viewer->givePermissionTo([
        'employees.view',
        'employee-compensation.view',
    ]);

    $employeeUser =
        User::factory()->create();

    $employee =
        EmployeeProfile::create([
            'employee_number' => 'EMP-000002',

            'user_id' => $employeeUser->id,

            'employment_type' => 'permanent',

            'status' => 'active',

            'hire_date' => '2026-01-01',
        ]);

    EmployeeCompensation::create([
        'employee_profile_id' => $employee->id,

        'currency' => 'LKR',

        'monthly_basic_salary' => '150000.00',

        'fixed_allowance' => '15000.00',

        'fixed_deduction' => '3000.00',

        'effective_from' => '2026-01-01',

        'active' => true,
    ]);

    $this
        ->actingAs($viewer)
        ->get(
            "/admin/hr/employees/{$employee->public_id}"
        )
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->where(
                    'canViewCompensation',
                    true
                )
                ->where(
                    'employee.compensation.currency',
                    'LKR'
                )
        );
});
