<?php

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('exports project expenses and approved non labor costs', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    ProjectExpense::create([
        'expense_number' => 'EXP-000001',

        'project_id' => $project->id,

        'type' => 'hosting',

        'title' => 'Hosting expense',

        'expense_date' => today(),

        'currency' => 'LKR',

        'subtotal' => 15000,

        'tax_amount' => 0,

        'total_amount' => 15000,

        'status' => 'approved',

        'created_by' => $admin->id,
    ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/expenses/export/excel"
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/expenses/export/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/non-labor-costs/export/excel"
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/non-labor-costs/export/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
