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

it('shows only outstanding invoices in receivables', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create();

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

    Invoice::create([
        'invoice_number' => 'INV-000002',

        'client_id' => $project->client_id,

        'status' => 'paid',

        'invoice_date' => today(),

        'due_date' => today(),

        'currency' => 'LKR',

        'billing_name' => $project->client->name,

        'total_amount' => 5000,

        'amount_paid' => 5000,

        'balance_due' => 0,
    ]);

    $this
        ->actingAs($admin)
        ->get(
            '/admin/receivables'
        )
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component(
                    'admin/receivables/index'
                )
                ->has(
                    'invoices',
                    1
                )
                ->where(
                    'summary.outstanding',
                    10000
                )
        );
});
