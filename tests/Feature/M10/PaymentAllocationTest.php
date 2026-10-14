<?php

use App\Models\Invoice;
use App\Models\Payment;
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

it('allocates payment to invoice and updates balances', function (): void {
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

            'total_amount' => 50000,

            'balance_due' => 50000,
        ]);

    $payment =
        Payment::create([
            'payment_number' => 'RCP-000001',

            'client_id' => $project->client_id,

            'payment_date' => today(),

            'currency' => 'LKR',

            'amount' => 50000,

            'allocated_amount' => 0,

            'unallocated_amount' => 50000,

            'method' => 'bank_transfer',

            'recorded_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/payments/{$payment->public_id}/allocations",
            [
                'invoice_id' => $invoice->id,

                'amount' => 20000,
            ]
        )
        ->assertRedirect();

    expect(
        (float) $invoice
            ->fresh()
            ->amount_paid
    )->toBe(20000.0);

    expect(
        (float) $invoice
            ->fresh()
            ->balance_due
    )->toBe(30000.0);

    expect(
        $invoice
            ->fresh()
            ->status
    )->toBe(
        'partially_paid'
    );

    expect(
        (float) $payment
            ->fresh()
            ->unallocated_amount
    )->toBe(30000.0);
});
