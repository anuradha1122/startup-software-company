<?php

use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('marks past due outstanding invoice overdue', function (): void {
    $project =
        Project::factory()->create();

    $invoice =
        Invoice::create([
            'invoice_number' => 'INV-000001',

            'client_id' => $project->client_id,

            'status' => 'issued',

            'invoice_date' => today()->subMonth(),

            'due_date' => today()->subDay(),

            'currency' => 'LKR',

            'billing_name' => $project->client->name,

            'total_amount' => 10000,

            'balance_due' => 10000,
        ]);

    $this
        ->artisan(
            'invoices:mark-overdue'
        )
        ->assertSuccessful();

    expect(
        $invoice
            ->fresh()
            ->status
    )->toBe('overdue');
});
