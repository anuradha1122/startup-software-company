<?php

use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('shows finance dashboard grouped by project currency', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    Project::factory()->create([
        'currency' => 'LKR',

        'contract_value' => 100000,

        'status' => 'active',
    ]);

    Project::factory()->create([
        'currency' => 'USD',

        'contract_value' => 1000,

        'status' => 'active',
    ]);

    $this
        ->actingAs($admin)
        ->get(
            '/admin/finance'
        )
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component(
                    'admin/finance/dashboard'
                )
                ->has(
                    'projects',
                    2
                )
                ->has(
                    'byCurrency',
                    2
                )
        );
});
