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

it('exports project profitability and finance portfolio', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'currency' => 'LKR',

            'contract_value' => 100000,
        ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/profitability/export/excel"
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/profitability/export/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );

    $this
        ->actingAs($admin)
        ->get(
            '/admin/finance/export/excel'
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            '/admin/finance/export/pdf'
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
