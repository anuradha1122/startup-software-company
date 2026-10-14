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

it('does not allow more than twenty four logged hours on one day', function (): void {
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

            'title' => 'Daily limit task',

            'created_by' => $user->id,
        ]);

    $weekStart = today()
        ->startOfWeek();

    $workDate = $weekStart
        ->copy()
        ->addDay()
        ->format('Y-m-d');

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

                'work_date' => $workDate,

                'hours' => 16,

                'description' => 'First entry',

                'billable' => true,
            ]
        )
        ->assertRedirect();

    $this
        ->actingAs($user)
        ->post(
            "/admin/timesheets/{$timesheet->public_id}/entries",
            [
                'project_id' => $project->id,

                'project_task_id' => $task->id,

                'work_date' => $workDate,

                'hours' => 9,

                'description' => 'Invalid excess entry',

                'billable' => true,
            ]
        )
        ->assertStatus(422);

    expect(
        (float) $timesheet
            ->entries()
            ->sum('hours')
    )->toBe(16.0);
});
