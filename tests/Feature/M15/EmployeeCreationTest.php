<?php

use App\Models\EmployeeProfile;
use App\Models\User;
use App\Services\Hr\EmployeeService;
use Database\Seeders\CompanySettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        CompanySettingSeeder::class
    );
});

it('creates one employee profile for user', function (): void {
    $actor =
        User::factory()->create();

    $employeeUser =
        User::factory()->create();

    $employee =
        app(
            EmployeeService::class
        )->create(
            [
                'user_id' => $employeeUser->id,

                'employment_type' => 'permanent',

                'status' => 'active',

                'department' => 'Engineering',

                'job_title' => 'Software Engineer',

                'hire_date' => '2026-01-01',

                'work_location' => 'Head Office',
            ],
            $actor
        );

    expect(
        $employee
    )->toBeInstanceOf(
        EmployeeProfile::class
    );

    expect(
        $employee->employee_number
    )->toStartWith('EMP-');

    expect(
        $employee->user_id
    )->toBe(
        $employeeUser->id
    );

    expect(
        $employee->employment_type
    )->toBe('permanent');

    expect(
        $employee->status
    )->toBe('active');
});

it('rejects second employee profile for same user', function (): void {
    $actor =
        User::factory()->create();

    $employeeUser =
        User::factory()->create();

    EmployeeProfile::create([
        'employee_number' => 'EMP-EXISTING',

        'user_id' => $employeeUser->id,

        'employment_type' => 'permanent',

        'status' => 'active',

        'hire_date' => '2025-01-01',
    ]);

    expect(
        fn () => app(
            EmployeeService::class
        )->create(
            [
                'user_id' => $employeeUser->id,

                'employment_type' => 'permanent',

                'status' => 'active',

                'hire_date' => '2026-01-01',
            ],
            $actor
        )
    )->toThrow(
        ValidationException::class
    );

    expect(
        EmployeeProfile::query()
            ->where(
                'user_id',
                $employeeUser->id
            )
            ->count()
    )->toBe(1);
});
