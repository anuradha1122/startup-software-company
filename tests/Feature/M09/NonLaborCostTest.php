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

it('includes only approved expenses in project non labor cost', function (): void {
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

        'type' => 'software',

        'title' => 'Approved expense',

        'expense_date' => today(),

        'currency' => 'LKR',

        'subtotal' => 10000,

        'tax_amount' => 0,

        'total_amount' => 10000,

        'status' => 'approved',

        'created_by' => $admin->id,
    ]);

    ProjectExpense::create([
        'expense_number' => 'EXP-000002',

        'project_id' => $project->id,

        'type' => 'travel',

        'title' => 'Pending expense',

        'expense_date' => today(),

        'currency' => 'LKR',

        'subtotal' => 50000,

        'tax_amount' => 0,

        'total_amount' => 50000,

        'status' => 'submitted',

        'created_by' => $admin->id,
    ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/non-labor-costs"
        )
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component(
                    'admin/projects/non-labor-costs/index'
                )
                ->where(
                    'summary.approved_count',
                    1
                )
                ->where(
                    'summary.total_non_labor_cost',
                    10000
                )
                ->has(
                    'expenses',
                    1
                )
        );
});
