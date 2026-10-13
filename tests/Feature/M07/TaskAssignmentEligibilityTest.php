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

it('allows task assignment to active project member', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $developer =
        User::factory()->create();

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $project
        ->assignments()
        ->create([
            'user_id' => $developer->id,

            'role' => 'Developer',

            'allocation_percent' => 100,

            'status' => 'active',

            'start_date' => today(),

            'billable' => true,

            'created_by' => $admin->id,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/projects/{$project->public_id}/tasks",
            [
                'parent_task_id' => null,

                'project_phase_id' => null,

                'project_milestone_id' => null,

                'title' => 'Assigned task',

                'description' => null,

                'type' => 'task',

                'priority' => 'medium',

                'assignee_id' => $developer->id,

                'estimated_hours' => 4,

                'planned_start_date' => today()
                    ->format('Y-m-d'),

                'due_date' => today()
                    ->addDays(2)
                    ->format('Y-m-d'),

                'client_visible' => false,

                'dependency_ids' => [],
            ]
        )
        ->assertRedirect();

    expect(
        $project
            ->tasks()
            ->firstOrFail()
            ->assignee_id
    )->toBe(
        $developer->id
    );
});

it('rejects task assignment to user outside project team', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $outsider =
        User::factory()->create();

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $this
        ->withoutExceptionHandling()
        ->actingAs($admin);

    expect(
        fn () => $this->post(
            "/admin/projects/{$project->public_id}/tasks",
            [
                'parent_task_id' => null,

                'project_phase_id' => null,

                'project_milestone_id' => null,

                'title' => 'Invalid assignment',

                'description' => null,

                'type' => 'task',

                'priority' => 'medium',

                'assignee_id' => $outsider->id,

                'estimated_hours' => 2,

                'planned_start_date' => today()
                    ->format('Y-m-d'),

                'due_date' => today()
                    ->addDay()
                    ->format('Y-m-d'),

                'client_visible' => false,

                'dependency_ids' => [],
            ]
        )
    )->toThrow(
        RuntimeException::class,
        'not an active member'
    );
});
