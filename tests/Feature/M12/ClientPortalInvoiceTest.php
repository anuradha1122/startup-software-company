<?php

use App\Models\ClientPortalUser;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows client portal to download its issued invoice', function (): void {
    $project =
        Project::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $project->client_id,

            'name' => 'Billing Contact',

            'email' => 'billing-portal@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    $invoice =
        Invoice::create([
            'invoice_number' => 'INV-M12-001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'status' => 'issued',

            'invoice_date' => today(),

            'due_date' => today()->addMonth(),

            'currency' => 'LKR',

            'billing_name' => $project
                ->client
                ->name,

            'total_amount' => 10000,

            'balance_due' => 10000,
        ]);

    $invoice
        ->lines()
        ->create([
            'sort_order' => 1,

            'description' => 'Client-facing service',

            'quantity' => 1,

            'unit_price' => 10000,

            'tax_rate' => 0,

            'subtotal' => 10000,

            'tax_amount' => 0,

            'total_amount' => 10000,
        ]);

    $this
        ->actingAs(
            $portalUser,
            'client_portal'
        )
        ->get(
            "/portal/invoices/{$invoice->public_id}/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
