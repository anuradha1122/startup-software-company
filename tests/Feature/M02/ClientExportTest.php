<?php

use App\Models\Client;
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

it('exports client list to excel', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $this
        ->actingAs($admin)
        ->get(
            '/admin/clients/export/excel'
        )
        ->assertOk();
});

it('exports client profile pdf', function (): void {
    $admin = User::factory()->create();
    $admin->assignRole('Admin');

    $client = Client::create([
        'client_number' => 'CLI-PDF01',
        'name' => 'PDF Client',
        'status' => 'active',
        'created_by' => $admin->id,
    ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/clients/{$client->public_id}/profile-pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
