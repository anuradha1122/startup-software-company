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

it('does not allow approved expense to be edited', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $expense =
        ProjectExpense::create([
            'expense_number' => 'EXP-000001',

            'project_id' => $project->id,

            'type' => 'software',

            'title' => 'Locked expense',

            'expense_date' => today(),

            'currency' => 'LKR',

            'subtotal' => 10000,

            'tax_amount' => 0,

            'total_amount' => 10000,

            'status' => 'approved',

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->put(
            "/admin/expenses/{$expense->public_id}",
            [
                'supplier_id' => null,

                'type' => 'software',

                'title' => 'Modified',

                'description' => null,

                'expense_date' => today()
                    ->format('Y-m-d'),

                'reference_number' => null,

                'currency' => 'LKR',

                'subtotal' => 5000,

                'tax_amount' => 0,

                'billable_to_client' => false,

                'incurred_by' => $admin->id,
            ]
        )
        ->assertForbidden();

    expect(
        $expense
            ->fresh()
            ->title
    )->toBe(
        'Locked expense'
    );
});
