<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('adds invoice line and recalculates invoice totals', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create();

    $invoice =
        Invoice::create([
            'invoice_number' => 'INV-000001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'status' => 'draft',

            'invoice_date' => today(),

            'due_date' => today()->addDays(30),

            'currency' => 'LKR',

            'billing_name' => $project->client->name,

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/invoices/{$invoice->public_id}/lines",
            [
                'description' => 'Development services',

                'quantity' => 2,

                'unit_price' => 10000,

                'tax_rate' => 18,
            ]
        )
        ->assertRedirect();

    $invoice->refresh();

    expect(
        (float) $invoice->subtotal
    )->toBe(20000.0);

    expect(
        (float) $invoice->tax_amount
    )->toBe(3600.0);

    expect(
        (float) $invoice->total_amount
    )->toBe(23600.0);
});
