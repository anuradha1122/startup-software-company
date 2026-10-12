<?php

use App\Models\Client;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('allows finance to add a billing profile', function (): void {
    $finance = User::factory()->create();
    $finance->assignRole('Finance');

    $client = Client::create([
        'client_number' => 'CLI-BILL01',
        'name' => 'Billing Client',
        'status' => 'active',
        'created_by' => $finance->id,
    ]);

    $this
        ->actingAs($finance)
        ->post(
            "/admin/clients/{$client->public_id}/billing-profiles",
            [
                'billing_name' => 'Billing Client Pvt Ltd',
                'billing_email' => 'accounts@example.com',
                'billing_phone' => '0110000000',
                'billing_address' => 'Colombo',
                'city' => 'Colombo',
                'country' => 'Sri Lanka',
                'tax_number' => 'TIN999',
                'currency' => 'LKR',
                'payment_terms_days' => 30,
                'invoice_notes' => 'Send by email',
                'is_default' => true,
            ]
        )
        ->assertRedirect();

    expect(
        $client
            ->billingProfiles()
            ->count()
    )->toBe(1);

    expect(
        $client
            ->billingProfiles()
            ->first()
            ->is_default
    )->toBeTrue();
});
