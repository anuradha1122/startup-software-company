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

it('does not allow issued invoice lines to be modified', function (): void {
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

            'status' => 'issued',

            'invoice_date' => today(),

            'due_date' => today()->addMonth(),

            'currency' => 'LKR',

            'billing_name' => $project->client->name,

            'total_amount' => 10000,

            'balance_due' => 10000,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/invoices/{$invoice->public_id}/lines",
            [
                'description' => 'Invalid edit',

                'quantity' => 1,

                'unit_price' => 5000,

                'tax_rate' => 0,
            ]
        )
        ->assertForbidden();
});
