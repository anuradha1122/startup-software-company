<?php

use App\Models\Project;
use App\Models\ResourceCostRate;
use App\Models\TaskWorkflowStatus;
use App\Models\Timesheet;
use App\Models\User;
use App\Notifications\TimesheetSubmittedNotification;
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

it('submits valid timesheet for review', function (): void {
    $user = User::factory()->create();

    $user->assignRole(
        'Project Manager'
    );

    $admin = User::factory()->create();

    $admin->assignRole('Admin');

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

    $workflowStatus =
        TaskWorkflowStatus::where(
            'slug',
            'in-progress'
        )->firstOrFail();

    $task = $project
        ->tasks()
        ->create([
            'task_sequence' => 1,

            'task_number' => "{$project->project_number}-T0001",

            'workflow_status_id' => $workflowStatus->id,

            'title' => 'Submission test task',

            'assignee_id' => $user->id,

            'created_by' => $admin->id,
        ]);

    $weekStart = today()
        ->startOfWeek();

    $workDate = $weekStart
        ->copy()
        ->addDay();

    $timesheet = Timesheet::create([
        'user_id' => $user->id,

        'week_start' => $weekStart,

        'week_end' => $weekStart
            ->copy()
            ->endOfWeek(),

        'status' => 'draft',
    ]);

    $timesheet
        ->entries()
        ->create([
            'project_id' => $project->id,

            'project_task_id' => $task->id,

            'project_assignment_id' => $assignment->id,

            'work_date' => $workDate,

            'hours' => 8,

            'description' => 'Development work',

            'billable' => true,
        ]);

    ResourceCostRate::create([
        'user_id' => $user->id,

        'currency' => 'LKR',

        'hourly_cost_rate' => 2000,

        'effective_from' => $weekStart
            ->copy()
            ->subMonth(),

        'effective_to' => null,

        'active' => true,

        'created_by' => $admin->id,
    ]);

    $this
        ->actingAs($user)
        ->post(
            "/admin/timesheets/{$timesheet->public_id}/submit"
        )
        ->assertRedirect();

    $timesheet->refresh();

    expect(
        $timesheet->status
    )->toBe('submitted');

    expect(
        $timesheet->submitted_at
    )->not->toBeNull();

    Notification::assertSentTo(
        $admin,
        TimesheetSubmittedNotification::class
    );
});

it('does not submit an empty timesheet', function (): void {
    $user = User::factory()->create();

    $user->assignRole(
        'Project Manager'
    );

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
            "/admin/timesheets/{$timesheet->public_id}/submit"
        )
        ->assertStatus(422);

    expect(
        $timesheet
            ->fresh()
            ->status
    )->toBe('draft');
});

it('does not submit when internal cost rate is missing', function (): void {
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

            'title' => 'Missing rate task',

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

    $timesheet
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
        ]);

    $this
        ->withoutExceptionHandling()
        ->actingAs($user);

    expect(
        fn () => $this->post(
            "/admin/timesheets/{$timesheet->public_id}/submit"
        )
    )->toThrow(
        RuntimeException::class,
        'No internal cost rate'
    );

    expect(
        $timesheet
            ->fresh()
            ->status
    )->toBe('draft');
});
