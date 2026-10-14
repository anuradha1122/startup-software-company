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

it('prevents modifying entries after timesheet approval', function (): void {
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
        'done'
    )->firstOrFail();

    $task = $project
        ->tasks()
        ->create([
            'task_sequence' => 1,

            'task_number' => "{$project->project_number}-T0001",

            'workflow_status_id' => $status->id,

            'title' => 'Locked entry task',

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

        'status' => 'approved',

        'reviewed_at' => now(),
    ]);

    $entry = $timesheet
        ->entries()
        ->create([
            'project_id' => $project->id,

            'project_task_id' => $task->id,

            'project_assignment_id' => $assignment->id,

            'work_date' => $weekStart
                ->copy()
                ->addDay(),

            'hours' => 8,

            'billable' => true,

            'hourly_cost_rate_snapshot' => 2000,

            'internal_cost_amount' => 16000,

            'cost_currency' => 'LKR',

            'costed_at' => now(),
        ]);

    $this
        ->actingAs($user)
        ->delete(
            "/admin/timesheets/{$timesheet->public_id}/entries/{$entry->public_id}"
        )
        ->assertForbidden();

    $this->assertDatabaseHas(
        'timesheet_entries',
        [
            'id' => $entry->id,
            'deleted_at' => null,
        ]
    );
});
