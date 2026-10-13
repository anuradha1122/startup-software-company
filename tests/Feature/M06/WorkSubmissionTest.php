<?php

use App\Models\Project;
use App\Models\TaskWorkflowStatus;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TaskWorkflowStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();

    $this->seed([
        RolePermissionSeeder::class,
        TaskWorkflowStatusSeeder::class,
    ]);
});

it('submits work and moves task to review', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $progress =
        TaskWorkflowStatus::where(
            'slug',
            'in-progress'
        )->firstOrFail();

    $task =
        $project
            ->tasks()
            ->create([
                'task_sequence' => 1,

                'task_number' => "{$project->project_number}-T0001",

                'workflow_status_id' => $progress->id,

                'title' => 'Build module',

                'assignee_id' => $admin->id,

                'created_by' => $admin->id,
            ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/tasks/{$task->public_id}/submissions",
            [
                'summary' => 'Module completed.',

                'testing_notes' => 'Feature tests pass.',

                'deployment_notes' => null,

                'reference_url' => null,
            ]
        )
        ->assertRedirect();

    $submission =
        $task
            ->submissions()
            ->firstOrFail();

    expect(
        $submission->version_number
    )->toBe(1);

    expect(
        $submission->status
    )->toBe('submitted');

    expect(
        $task
            ->fresh()
            ->workflowStatus
            ->slug
    )->toBe('review');
});
