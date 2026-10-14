<?php

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use App\Services\Billing\PaymentAllocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('marks invoice paid after full allocation', function (): void {
    $user =
        User::factory()->create();

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

            'total_amount' => 25000,

            'balance_due' => 25000,
        ]);

    $payment =
        Payment::create([
            'payment_number' => 'RCP-000001',

            'client_id' => $project->client_id,

            'payment_date' => today(),

            'currency' => 'LKR',

            'amount' => 25000,

            'allocated_amount' => 0,

            'unallocated_amount' => 25000,

            'method' => 'bank_transfer',
        ]);

    app(
        PaymentAllocationService::class
    )->allocate(
        $payment,
        $invoice,
        25000,
        $user
    );

    $invoice->refresh();

    expect(
        $invoice->status
    )->toBe('paid');

    expect(
        (float) $invoice->balance_due
    )->toBe(0.0);

    expect(
        $invoice->paid_at
    )->not->toBeNull();
});
