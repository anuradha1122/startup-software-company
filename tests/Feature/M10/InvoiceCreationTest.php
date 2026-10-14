<?php

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        CompanySettingSeeder::class,
    ]);
});

it('creates draft project invoice', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $this
        ->actingAs($admin)
        ->post(
            '/admin/invoices',
            [
                'project_id' => $project->id,

                'billing_contact_id' => null,

                'invoice_date' => today()
                    ->format('Y-m-d'),

                'due_date' => today()
                    ->addDays(30)
                    ->format('Y-m-d'),

                'currency' => $project->currency,

                'billing_name' => $project
                    ->client
                    ->name,

                'billing_email' => null,

                'billing_address' => $project
                    ->client
                    ->address,

                'notes' => null,

                'payment_instructions' => 'Bank transfer',
            ]
        )
        ->assertRedirect();

    $invoice =
        Invoice::firstOrFail();

    expect(
        $invoice->invoice_number
    )->toStartWith('INV-');

    expect(
        $invoice->client_id
    )->toBe(
        $project->client_id
    );

    expect(
        $invoice->status
    )->toBe('draft');

    expect(
        (float) $invoice->total_amount
    )->toBe(0.0);
});
