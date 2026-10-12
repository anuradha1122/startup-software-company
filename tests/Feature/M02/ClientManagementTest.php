<?php

use App\Models\Client;
use App\Models\User;
use App\Notifications\ClientCreatedNotification;
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

it('allows sales to create a client', function (): void {
    Notification::fake();

    $sales = User::factory()->create();
    $sales->assignRole('Sales');

    $this
        ->actingAs($sales)
        ->post(
            '/admin/clients',
            [
                'name' => 'Acme Ltd',
                'legal_name' => 'Acme Private Limited',
                'registration_number' => 'PV1234',
                'tax_number' => 'TIN1234',
                'email' => 'info@acme.test',
                'phone' => '0112345678',
                'website' => 'https://acme.test',
                'address' => 'Colombo',
                'city' => 'Colombo',
                'country' => 'Sri Lanka',
                'default_currency' => 'LKR',
                'default_payment_terms_days' => 30,
                'status' => 'active',
                'owner_id' => $sales->id,
                'notes' => 'Important client',
            ]
        )
        ->assertRedirect();

    $client = Client::where(
        'name',
        'Acme Ltd'
    )->firstOrFail();

    expect(
        $client->client_number
    )->toStartWith('CLI-');

    Notification::assertSentTo(
        $sales,
        ClientCreatedNotification::class
    );
});
