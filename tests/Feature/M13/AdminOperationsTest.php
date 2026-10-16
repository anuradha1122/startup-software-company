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

it('shows operational health dashboard to administrator', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $this
        ->actingAs($admin)
        ->get(
            '/admin/operations'
        )
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component(
                    'admin/operations/index'
                )
                ->has(
                    'health.status'
                )
                ->has(
                    'health.checks',
                    5
                )
                ->has(
                    'health.issue_count'
                )
        );
});
