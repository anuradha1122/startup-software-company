<?php

use App\Models\Client;
use App\Models\User;
use App\Notifications\ClientContactAddedNotification;
use Database\Seeders\CompanySettingSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        CompanySettingSeeder::class,
    ]);
});

it('adds a client contact', function (): void {
    Notification::fake();

    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $client = Client::create([
        'client_number' => 'CLI-TEST01',
        'name' => 'Acme',
        'status' => 'active',
        'owner_id' => $sales->id,
        'created_by' => $sales->id,
    ]);

    $this
        ->actingAs($sales)
        ->post(
            "/admin/clients/{$client->public_id}/contacts",
            [
                'name' => 'Jane Client',
                'designation' => 'Director',
                'email' => 'jane@example.com',
                'phone' => '0771234567',
                'is_primary' => true,
                'is_billing_contact' => true,
                'is_decision_maker' => true,
                'status' => 'active',
                'notes' => null,
            ]
        )
        ->assertRedirect();

    $contact = $client
        ->contacts()
        ->firstOrFail();

    expect(
        $contact->is_primary
    )->toBeTrue();

    Notification::assertSentTo(
        $sales,
        ClientContactAddedNotification::class
    );
});
