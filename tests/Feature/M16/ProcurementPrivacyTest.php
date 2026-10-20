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

it('blocks procurement exports from unauthorized internal user', function (): void {
    $user =
        User::factory()->create();

    $this
        ->actingAs($user)
        ->get(
            '/admin/procurement/requests/export/excel'
        )
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->get(
            '/admin/purchase-orders/export/excel'
        )
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->get(
            '/admin/assets/export/excel'
        )
        ->assertForbidden();
});

it('allows authorized user to reach procurement export endpoints', function (): void {
    $user =
        User::factory()->create();

    $user->givePermissionTo([
        'procurement-requests.export',
        'purchase-orders.export',
        'assets.export',
    ]);

    $this
        ->actingAs($user)
        ->get(
            '/admin/procurement/requests/export/excel'
        )
        ->assertSuccessful();

    $this
        ->actingAs($user)
        ->get(
            '/admin/purchase-orders/export/excel'
        )
        ->assertSuccessful();

    $this
        ->actingAs($user)
        ->get(
            '/admin/assets/export/excel'
        )
        ->assertSuccessful();
});
