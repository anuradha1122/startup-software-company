<?php

use App\Models\EmployeeCompensation;
use App\Models\EmployeeProfile;
use App\Models\User;
use App\Services\Hr\EmployeeCompensationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('keeps previous compensation as historical record when salary changes', function (): void {
    $actor =
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

    $oldCompensation =
        EmployeeCompensation::create([
            'employee_profile_id' => $employee->id,

            'currency' => 'LKR',

            'monthly_basic_salary' => '100000.00',

            'fixed_allowance' => '5000.00',

            'fixed_deduction' => '1000.00',

            'effective_from' => '2025-01-01',

            'effective_to' => null,

            'active' => true,

            'created_by' => $actor->id,
        ]);

    $newCompensation =
        app(
            EmployeeCompensationService::class
        )->replaceActive(
            $employee,
            [
                'currency' => 'LKR',

                'monthly_basic_salary' => '125000.00',

                'fixed_allowance' => '7500.00',

                'fixed_deduction' => '1500.00',

                'effective_from' => '2026-10-01',

                'notes' => 'Annual salary revision.',
            ],
            $actor
        );

    $oldCompensation->refresh();

    expect(
        $oldCompensation->active
    )->toBeFalse();

    expect(
        $oldCompensation
            ->effective_to
    )->not->toBeNull();

    expect(
        $oldCompensation
            ->monthly_basic_salary
    )->toBe('100000.00');

    expect(
        $newCompensation->active
    )->toBeTrue();

    expect(
        $newCompensation
            ->monthly_basic_salary
    )->toBe('125000.00');

    expect(
        EmployeeCompensation::query()
            ->where(
                'employee_profile_id',
                $employee->id
            )
            ->count()
    )->toBe(2);

    expect(
        EmployeeCompensation::query()
            ->where(
                'employee_profile_id',
                $employee->id
            )
            ->where(
                'active',
                true
            )
            ->count()
    )->toBe(1);

    expect(
        EmployeeCompensation::query()
            ->where(
                'employee_profile_id',
                $employee->id
            )
            ->where(
                'active',
                false
            )
            ->count()
    )->toBe(1);
});
