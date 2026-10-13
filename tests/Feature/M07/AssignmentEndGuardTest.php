<?php

use App\Models\Project;
use App\Models\TaskWorkflowStatus;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TaskWorkflowStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        RolePermissionSeeder::class,
        TaskWorkflowStatusSeeder::class,
    ]);
});

it('does not end assignment while user owns open tasks', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $developer =
        User::factory()->create();

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $assignment =
        $project
            ->assignments()
            ->create([
                'user_id' => $developer->id,

                'role' => 'Developer',

                'allocation_percent' => 100,

                'status' => 'active',

                'billable' => true,

                'created_by' => $admin->id,
            ]);

    $status =
        TaskWorkflowStatus::where(
            'slug',
            'in-progress'
        )->firstOrFail();

    $project
        ->tasks()
        ->create([
            'task_sequence' => 1,

            'task_number' => "{$project->project_number}-T0001",

            'workflow_status_id' => $status->id,

            'title' => 'Open task',

            'assignee_id' => $developer->id,

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/projects/{$project->public_id}/team/{$assignment->public_id}/end"
        )
        ->assertStatus(409);

    expect(
        $assignment
            ->fresh()
            ->status
    )->toBe('active');
});
