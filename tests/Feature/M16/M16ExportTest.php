<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('exports procurement purchase order asset and assignment registers', function (): void {
    $user =
        User::factory()->create();

    $user->givePermissionTo([
        'procurement-requests.export',
        'purchase-orders.export',
        'assets.export',
        'asset-assignments.export',
    ]);

    $this
        ->actingAs($user)
        ->get(
            '/admin/procurement/requests/export/excel'
        )
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(
            '/admin/purchase-orders/export/excel'
        )
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(
            '/admin/assets/export/excel'
        )
        ->assertOk();

    $this
        ->actingAs($user)
        ->get(
            '/admin/assets/assignments/export/excel'
        )
        ->assertOk();
});
