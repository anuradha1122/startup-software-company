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

it('exports invoice pdf and invoice register excel', function (): void {
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

    $invoice
        ->lines()
        ->create([
            'sort_order' => 1,

            'description' => 'Services',

            'quantity' => 1,

            'unit_price' => 10000,

            'tax_rate' => 0,

            'subtotal' => 10000,

            'tax_amount' => 0,

            'total_amount' => 10000,
        ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/invoices/{$invoice->public_id}/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );

    $this
        ->actingAs($admin)
        ->get(
            '/admin/invoices/export/excel'
        )
        ->assertOk();
});
