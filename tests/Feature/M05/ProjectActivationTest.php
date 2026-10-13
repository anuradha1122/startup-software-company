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

it('requires approved charter before activation', function (): void {
    $pm =
        User::factory()->create();

    $pm->assignRole(
        'Project Manager'
    );

    $project =
        Project::factory()
            ->create([
                'status' => 'planned',

                'project_manager_id' => $pm->id,

                'approved_charter_id' => null,
            ]);

    $this
        ->actingAs($pm)
        ->post(
            "/admin/projects/{$project->public_id}/activate"
        )
        ->assertStatus(422);
});

it('activates project after charter approval', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()
            ->create([
                'status' => 'planned',
            ]);

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
        ->post(
            "/admin/projects/{$project->public_id}/activate"
        )
        ->assertRedirect();

    expect(
        $project->fresh()->status
    )->toBe('active');
});
