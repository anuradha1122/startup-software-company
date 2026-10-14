<?php

use App\Models\Project;
use App\Models\TaskWorkflowStatus;
use App\Models\Timesheet;
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

it('rejects time entry when user has no project assignment for work date', function (): void {
    $user = User::factory()->create();

    $user->assignRole(
        'Project Manager'
    );

    $project = Project::factory()->create([
        'status' => 'active',
    ]);

    $status = TaskWorkflowStatus::where(
        'slug',
        'in-progress'
    )->firstOrFail();

    $task = $project
        ->tasks()
        ->create([
            'task_sequence' => 1,

            'task_number' => "{$project->project_number}-T0001",

            'workflow_status_id' => $status->id,

            'title' => 'No assignment task',

            'created_by' => $user->id,
        ]);

    $weekStart = today()
        ->startOfWeek();

    $timesheet = Timesheet::create([
        'user_id' => $user->id,

        'week_start' => $weekStart,

        'week_end' => $weekStart
            ->copy()
            ->endOfWeek(),

        'status' => 'draft',
    ]);

    $this
        ->actingAs($user)
        ->post(
            "/admin/timesheets/{$timesheet->public_id}/entries",
            [
                'project_id' => $project->id,

                'project_task_id' => $task->id,

                'work_date' => $weekStart
                    ->copy()
                    ->addDay()
                    ->format('Y-m-d'),

                'hours' => 8,

                'description' => 'Should fail',

                'billable' => true,
            ]
        )
        ->assertStatus(422);

    expect(
        $timesheet
            ->entries()
            ->count()
    )->toBe(0);
});

it('accepts historical time within an ended assignment period', function (): void {
    $user = User::factory()->create();

    $user->assignRole(
        'Project Manager'
    );

    $project = Project::factory()->create([
        'status' => 'active',
    ]);

    $project
        ->assignments()
        ->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'role' => 'Developer',

                'allocation_percent' => 100,

                'status' => 'ended',

                'start_date' => '2026-09-01',

                'end_date' => '2026-09-30',

                'billable' => true,

                'created_by' => $user->id,

                'ended_by' => $user->id,

                'ended_at' => now(),
            ]
        );

    $status = TaskWorkflowStatus::where(
        'slug',
        'done'
    )->firstOrFail();

    $task = $project
        ->tasks()
        ->create([
            'task_sequence' => 1,

            'task_number' => "{$project->project_number}-T0001",

            'workflow_status_id' => $status->id,

            'title' => 'Historical assignment task',

            'created_by' => $user->id,
        ]);

    $timesheet = Timesheet::create([
        'user_id' => $user->id,

        'week_start' => '2026-09-07',

        'week_end' => '2026-09-13',

        'status' => 'draft',
    ]);

    $this
        ->actingAs($user)
        ->post(
            "/admin/timesheets/{$timesheet->public_id}/entries",
            [
                'project_id' => $project->id,

                'project_task_id' => $task->id,

                'work_date' => '2026-09-09',

                'hours' => 8,

                'description' => 'Historical work',

                'billable' => true,
            ]
        )
        ->assertRedirect();

    expect(
        $timesheet
            ->entries()
            ->count()
    )->toBe(1);
});
