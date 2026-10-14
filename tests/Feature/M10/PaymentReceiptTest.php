<?php

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

it('downloads payment receipt pdf', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create();

    $payment =
        Payment::create([
            'payment_number' => 'RCP-000001',

            'client_id' => $project->client_id,

            'payment_date' => today(),

            'currency' => 'LKR',

            'amount' => 10000,

            'allocated_amount' => 0,

            'unallocated_amount' => 10000,

            'method' => 'bank_transfer',

            'recorded_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/payments/{$payment->public_id}/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
