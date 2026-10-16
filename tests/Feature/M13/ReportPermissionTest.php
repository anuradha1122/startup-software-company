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

it('blocks user without reports permission', function (): void {
    $user =
        User::factory()->create();

    $this
        ->actingAs($user)
        ->get(
            '/admin/reports'
        )
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->get(
            '/admin/operations'
        )
        ->assertForbidden();
});
