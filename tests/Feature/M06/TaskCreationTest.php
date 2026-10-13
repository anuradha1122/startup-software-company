<?php

use App\Models\Project;
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

it('creates task inside active project', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/projects/{$project->public_id}/tasks",
            [
                'parent_task_id' => null,
                'project_phase_id' => null,
                'project_milestone_id' => null,

                'title' => 'Build authentication API',

                'description' => 'Implement backend authentication endpoints.',

                'type' => 'task',
                'priority' => 'high',

                'assignee_id' => $admin->id,

                'estimated_hours' => 8,

                'planned_start_date' => now()->format('Y-m-d'),

                'due_date' => now()
                    ->addDays(3)
                    ->format('Y-m-d'),

                'client_visible' => false,

                'dependency_ids' => [],
            ]
        )
        ->assertRedirect();

    $task =
        $project
            ->tasks()
            ->firstOrFail();

    expect(
        $task->task_number
    )->toBe(
        "{$project->project_number}-T0001"
    );

    expect(
        $task
            ->workflowStatus
            ->is_initial
    )->toBeTrue();

    expect(
        $task
            ->transitions()
            ->count()
    )->toBe(1);
});
