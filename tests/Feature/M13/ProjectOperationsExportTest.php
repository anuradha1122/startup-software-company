<?php

use App\Models\Project;
use App\Models\ReportRun;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('exports project operations to excel and pdf and records runs', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    Project::factory()->create([
        'status' => 'active',

        'currency' => 'LKR',

        'contract_value' => 100000,
    ]);

    $this
        ->actingAs($admin)
        ->get(
            '/admin/reports/project-operations/export/excel'
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            '/admin/reports/project-operations/export/pdf'
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );

    expect(
        ReportRun::where(
            'report_type',
            'project_operations'
        )->count()
    )->toBe(2);

    expect(
        ReportRun::where(
            'status',
            'completed'
        )->count()
    )->toBe(2);
});
