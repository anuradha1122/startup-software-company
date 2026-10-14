<?php

use App\Models\Invoice;
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

it('imports approved billable M09 expense into draft invoice once', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'currency' => 'LKR',
        ]);

    $expense =
        ProjectExpense::create([
            'expense_number' => 'EXP-000001',

            'project_id' => $project->id,

            'type' => 'hosting',

            'title' => 'Cloud hosting',

            'expense_date' => today(),

            'currency' => 'LKR',

            'subtotal' => 10000,

            'tax_amount' => 0,

            'total_amount' => 10000,

            'billable_to_client' => true,

            'status' => 'approved',
        ]);

    $invoice =
        Invoice::create([
            'invoice_number' => 'INV-000001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'status' => 'draft',

            'invoice_date' => today(),

            'due_date' => today()->addMonth(),

            'currency' => 'LKR',

            'billing_name' => $project->client->name,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/invoices/{$invoice->public_id}/expenses/{$expense->public_id}/import"
        )
        ->assertRedirect();

    $line =
        $invoice
            ->lines()
            ->firstOrFail();

    expect(
        $line->source_type
    )->toBe(
        'project_expense'
    );

    expect(
        $line->source_id
    )->toBe(
        $expense->id
    );

    expect(
        (float) $invoice
            ->fresh()
            ->total_amount
    )->toBe(10000.0);
});
