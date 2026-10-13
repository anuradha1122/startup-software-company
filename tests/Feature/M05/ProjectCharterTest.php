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

it('approves charter and prevents editing approved version', function (): void {
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

            'status' => 'draft',

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/projects/{$project->public_id}/charters/{$charter->public_id}/approve"
        )
        ->assertRedirect();

    $project->refresh();
    $charter->refresh();

    expect(
        $charter->status
    )->toBe('approved');

    expect(
        $project->approved_charter_id
    )->toBe($charter->id);
});
