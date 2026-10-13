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

it('exports project assignments to excel and pdf', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $person =
        User::factory()->create();

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $project
        ->assignments()
        ->create([
            'user_id' => $person->id,

            'role' => 'Developer',

            'allocation_percent' => 100,

            'status' => 'active',

            'billable' => true,

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/team/export/excel"
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/team/export/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
