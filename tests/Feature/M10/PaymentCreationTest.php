<?php

use App\Models\Client;
use App\Models\Payment;
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

it('records client payment with receipt number', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $client =
        Client::query()->first()
        ?? Client::create([
            'client_number' => 'CLI-000001',

            'name' => 'Test Client',

            'status' => 'active',
        ]);

    $this
        ->actingAs($admin)
        ->post(
            '/admin/payments',
            [
                'client_id' => $client->id,

                'payment_date' => today()
                    ->format('Y-m-d'),

                'currency' => 'LKR',

                'amount' => 50000,

                'method' => 'bank_transfer',

                'reference_number' => 'BANK-123',

                'notes' => null,
            ]
        )
        ->assertRedirect();

    $payment =
        Payment::firstOrFail();

    expect(
        $payment->payment_number
    )->toStartWith('RCP-');

    expect(
        (float) $payment->amount
    )->toBe(50000.0);

    expect(
        (float) $payment
            ->unallocated_amount
    )->toBe(50000.0);
});
