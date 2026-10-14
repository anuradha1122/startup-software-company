<?php

use App\Mail\InvoiceIssuedMail;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Mail::fake();
    Notification::fake();

    $this->seed(
        RolePermissionSeeder::class
    );
});

it('issues invoice and queues client email', function (): void {
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

            'billing_email' => 'billing@example.com',

            'created_by' => $admin->id,
        ]);

    $invoice
        ->lines()
        ->create([
            'sort_order' => 1,

            'description' => 'Development',

            'quantity' => 1,

            'unit_price' => 50000,

            'tax_rate' => 0,

            'subtotal' => 50000,

            'tax_amount' => 0,

            'total_amount' => 50000,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/invoices/{$invoice->public_id}/issue"
        )
        ->assertRedirect();

    $invoice->refresh();

    expect(
        $invoice->status
    )->toBe('issued');

    expect(
        (float) $invoice->balance_due
    )->toBe(50000.0);

    expect(
        $invoice->issued_at
    )->not->toBeNull();

    Mail::assertQueued(
        InvoiceIssuedMail::class
    );
});
