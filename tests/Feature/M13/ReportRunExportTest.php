<?php

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

it('exports report run history', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    ReportRun::create([
        'report_type' => 'project_operations',

        'format' => 'excel',

        'status' => 'completed',

        'download_name' => 'project-operations.xlsx',

        'row_count' => 3,

        'generated_by' => $admin->id,

        'generated_at' => now(),
    ]);

    $this
        ->actingAs($admin)
        ->get(
            '/admin/reports/runs/export/excel'
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            '/admin/reports/runs/export/pdf'
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
