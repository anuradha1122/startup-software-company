<?php

use App\Models\Project;
use App\Models\ProjectCharter;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        RolePermissionSeeder::class
    );
});

it('exports projects to excel', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    Project::factory()->create();

    $this
        ->actingAs($admin)
        ->get(
            '/admin/projects/export/excel'
        )
        ->assertOk();
});

it('exports approved project charter pdf', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create();

    $charter =
        ProjectCharter::create([
            'project_id' => $project->id,

            'version_number' => 1,

            'status' => 'approved',

            'created_by' => $admin->id,

            'approved_by' => $admin->id,

            'approved_at' => now(),
        ]);

    $project->update([
        'approved_charter_id' => $charter->id,
    ]);

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/charter-pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});
