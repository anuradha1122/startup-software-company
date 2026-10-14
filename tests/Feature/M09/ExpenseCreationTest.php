<?php

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\User;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        CompanySettingSeeder::class,
    ]);
});

it('creates project expense in draft status', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/projects/{$project->public_id}/expenses",
            [
                'supplier_id' => null,

                'type' => 'software',

                'title' => 'Cloud subscription',

                'description' => 'Monthly software cost',

                'expense_date' => today()
                    ->format('Y-m-d'),

                'reference_number' => 'INV-001',

                'currency' => 'LKR',

                'subtotal' => 10000,

                'tax_amount' => 1800,

                'billable_to_client' => false,

                'incurred_by' => $admin->id,
            ]
        )
        ->assertRedirect();

    $expense =
        ProjectExpense::firstOrFail();

    expect(
        $expense->expense_number
    )->toStartWith('EXP-');

    expect(
        $expense->status
    )->toBe('draft');

    expect(
        (float) $expense->total_amount
    )->toBe(11800.0);
});
