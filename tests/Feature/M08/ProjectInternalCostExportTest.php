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

it('exports approved project internal costs to excel and pdf', function (): void {
    $admin = User::factory()->create();

    $admin->assignRole('Admin');

    $user = User::factory()->create();

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

                'created_by' => $admin->id,
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

            'title' => 'Cost export task',

            'created_by' => $admin->id,
        ]);

    $weekStart = today()
        ->startOfWeek();

    $approvedTimesheet =
        Timesheet::create([
            'user_id' => $user->id,

            'week_start' => $weekStart,

            'week_end' => $weekStart
                ->copy()
                ->endOfWeek(),

            'status' => 'approved',

            'reviewed_by' => $admin->id,

            'reviewed_at' => now(),
        ]);

    $approvedTimesheet
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
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/internal-costs/export/excel"
        )
        ->assertOk();

    $this
        ->actingAs($admin)
        ->get(
            "/admin/projects/{$project->public_id}/internal-costs/export/pdf"
        )
        ->assertOk()
        ->assertHeader(
            'content-type',
            'application/pdf'
        );
});

it('excludes unapproved timesheet entries from internal project cost view', function (): void {
    $admin = User::factory()->create();

    $admin->assignRole('Admin');

    $user = User::factory()->create();

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

                'created_by' => $admin->id,
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

            'title' => 'Unapproved cost task',

            'created_by' => $admin->id,
        ]);

    $weekStart = today()
        ->startOfWeek();

    $submittedTimesheet =
        Timesheet::create([
            'user_id' => $user->id,

            'week_start' => $weekStart,

            'week_end' => $weekStart
                ->copy()
                ->endOfWeek(),

            'status' => 'submitted',

            'submitted_at' => now(),
        ]);

    $submittedTimesheet
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

    $response =
        $this
            ->actingAs($admin)
            ->get(
                "/admin/projects/{$project->public_id}/internal-costs"
            );

    $response
        ->assertOk()
        ->assertInertia(
            fn ($page) => $page
                ->component(
                    'admin/projects/internal-costs/index'
                )
                ->where(
                    'summary.hours',
                    0
                )
                ->where(
                    'summary.internal_cost',
                    0
                )
                ->has(
                    'entries',
                    0
                )
        );
});
