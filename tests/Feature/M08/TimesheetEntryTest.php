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

it('records time only against valid project assignment and task', function (): void {
    $user = User::factory()->create();

    $user->assignRole(
        'Project Manager'
    );

    $project = Project::factory()->create([
        'status' => 'active',
    ]);

    $assignment = $project
        ->assignments()
        ->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'role' => 'Developer',
                'allocation_percent' => 100,
                'status' => 'active',
                'start_date' => today()
                    ->subMonth(),
                'end_date' => today()
                    ->addMonth(),
                'billable' => true,
                'created_by' => $user->id,
            ]
        );

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

            'title' => 'Implement M08',

            'assignee_id' => $user->id,

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

    $workDate = $weekStart
        ->copy()
        ->addDay();

    $this
        ->actingAs($user)
        ->post(
            "/admin/timesheets/{$timesheet->public_id}/entries",
            [
                'project_id' => $project->id,

                'project_task_id' => $task->id,

                'work_date' => $workDate
                    ->format('Y-m-d'),

                'hours' => 8,

                'description' => 'Implemented the timesheet module.',

                'billable' => true,
            ]
        )
        ->assertRedirect();

    $entry = $timesheet
        ->entries()
        ->firstOrFail();

    expect(
        $entry->project_id
    )->toBe($project->id);

    expect(
        $entry->project_task_id
    )->toBe($task->id);

    expect(
        $entry->project_assignment_id
    )->toBe($assignment->id);

    expect(
        (float) $entry->hours
    )->toBe(8.0);

    expect(
        $entry->description
    )->toBe(
        'Implemented the timesheet module.'
    );

    expect(
        $entry->billable
    )->toBeTrue();

    expect(
        $entry->hourly_cost_rate_snapshot
    )->toBeNull();

    expect(
        $entry->internal_cost_amount
    )->toBeNull();
});

it('rejects time entry when task belongs to another project', function (): void {
    $user = User::factory()->create();

    $user->assignRole(
        'Project Manager'
    );

    $projectA = Project::factory()->create([
        'status' => 'active',
    ]);

    $projectB = Project::factory()->create([
        'status' => 'active',
    ]);

    $projectA
        ->assignments()
        ->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'role' => 'Developer',
                'allocation_percent' => 100,
                'status' => 'active',
                'start_date' => today()
                    ->subMonth(),
                'end_date' => today()
                    ->addMonth(),
                'billable' => true,
                'created_by' => $user->id,
            ]
        );

    $status = TaskWorkflowStatus::where(
        'slug',
        'in-progress'
    )->firstOrFail();

    $task = $projectB
        ->tasks()
        ->create([
            'task_sequence' => 1,

            'task_number' => "{$projectB->project_number}-T0001",

            'workflow_status_id' => $status->id,

            'title' => 'Wrong project task',

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
                'project_id' => $projectA->id,

                'project_task_id' => $task->id,

                'work_date' => $weekStart
                    ->copy()
                    ->addDay()
                    ->format('Y-m-d'),

                'hours' => 4,

                'description' => 'Invalid entry',

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

it('rejects entry outside the timesheet week', function (): void {
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
                'status' => 'active',
                'start_date' => today()
                    ->subMonth(),
                'end_date' => today()
                    ->addMonth(),
                'billable' => true,
                'created_by' => $user->id,
            ]
        );

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

            'title' => 'Date validation task',

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
                    ->subDay()
                    ->format('Y-m-d'),

                'hours' => 4,

                'description' => 'Outside week',

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
