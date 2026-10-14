<?php

use App\Models\Supplier;
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

it('creates supplier with document number', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $this
        ->actingAs($admin)
        ->post(
            '/admin/suppliers',
            [
                'name' => 'Cloud Hosting Ltd',

                'legal_name' => 'Cloud Hosting Limited',

                'email' => 'billing@example.com',

                'phone' => '0111234567',

                'website' => 'https://example.com',

                'tax_number' => 'VAT-001',

                'address' => 'Colombo',

                'city' => 'Colombo',

                'country' => 'Sri Lanka',

                'default_currency' => 'LKR',

                'notes' => null,
            ]
        )
        ->assertRedirect();

    $supplier =
        Supplier::firstOrFail();

    expect(
        $supplier->supplier_number
    )->toStartWith('SUP-');

    expect(
        $supplier->name
    )->toBe(
        'Cloud Hosting Ltd'
    );
});
