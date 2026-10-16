<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('exports employee leave attendance and payroll registers', function (): void {
    $user =
        User::factory()->create();

    $user->givePermissionTo([
        'employees.export',
        'leave-requests.export',
        'attendance.export',
        'payroll.export',
    ]);

    $this
        ->actingAs($user)
        ->get(
            '/admin/hr/employees/export/excel'
        )
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(
            '/admin/hr/leave/export/excel'
        )
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(
            '/admin/hr/attendance/export/excel'
        )
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(
            '/admin/hr/payroll/export/excel'
        )
        ->assertOk();
});
