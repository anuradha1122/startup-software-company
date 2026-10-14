<?php

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use App\Services\Billing\PaymentAllocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rejects allocation above invoice balance', function (): void {
    $user =
        User::factory()->create();

    $project =
        Project::factory()->create();

    $invoice =
        Invoice::create([
            'invoice_number' => 'INV-000001',

            'client_id' => $project->client_id,

            'status' => 'issued',

            'invoice_date' => today(),

            'due_date' => today()->addMonth(),

            'currency' => 'LKR',

            'billing_name' => $project->client->name,

            'total_amount' => 10000,

            'balance_due' => 10000,
        ]);

    $payment =
        Payment::create([
            'payment_number' => 'RCP-000001',

            'client_id' => $project->client_id,

            'payment_date' => today(),

            'currency' => 'LKR',

            'amount' => 20000,

            'unallocated_amount' => 20000,

            'method' => 'bank_transfer',
        ]);

    expect(
        fn () => app(
            PaymentAllocationService::class
        )->allocate(
            $payment,
            $invoice,
            15000,
            $user
        )
    )->toThrow(
        RuntimeException::class,
        'invoice balance'
    );
});
