<?php

use App\Models\Project;
use App\Models\ResourceCostRate;
use App\Models\TaskWorkflowStatus;
use App\Models\Timesheet;
use App\Models\User;
use App\Notifications\TimesheetApprovedNotification;
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

it('approves timesheet and snapshots internal labor cost', function (): void {
    $admin = User::factory()->create();

    $admin->assignRole('Admin');

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

            'title' => 'Approval costing task',

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

        'status' => 'submitted',

        'submitted_at' => now(),
    ]);

    $entry = $timesheet
        ->entries()
        ->create([
            'project_id' => $project->id,

            'project_task_id' => $task->id,

            'project_assignment_id' => $assignment->id,

            'work_date' => $workDate,

            'hours' => 8,

            'description' => 'Eight hours of work',

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
        ->actingAs($admin)
        ->post(
            "/admin/timesheets/{$timesheet->public_id}/review",
            [
                'decision' => 'approved',

                'review_notes' => 'Approved.',
            ]
        )
        ->assertRedirect();

    $timesheet->refresh();
    $entry->refresh();

    expect(
        $timesheet->status
    )->toBe('approved');

    expect(
        $timesheet->reviewed_by
    )->toBe($admin->id);

    expect(
        $timesheet->reviewed_at
    )->not->toBeNull();

    expect(
        (float) $entry
            ->hourly_cost_rate_snapshot
    )->toBe(2000.0);

    expect(
        (float) $entry
            ->internal_cost_amount
    )->toBe(16000.0);

    expect(
        $entry->cost_currency
    )->toBe('LKR');

    expect(
        $entry->costed_at
    )->not->toBeNull();

    Notification::assertSentTo(
        $user,
        TimesheetApprovedNotification::class
    );
});

it('preserves approved cost snapshot when a later rate is created', function (): void {
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

                'start_date' => '2026-01-01',

                'end_date' => '2026-12-31',

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

            'title' => 'Historical cost task',

            'created_by' => $admin->id,
        ]);

    $timesheet = Timesheet::create([
        'user_id' => $user->id,

        'week_start' => '2026-06-01',

        'week_end' => '2026-06-07',

        'status' => 'approved',

        'reviewed_by' => $admin->id,

        'reviewed_at' => now(),
    ]);

    $entry = $timesheet
        ->entries()
        ->create([
            'project_id' => $project->id,

            'project_task_id' => $task->id,

            'project_assignment_id' => $assignment->id,

            'work_date' => '2026-06-02',

            'hours' => 8,

            'billable' => true,

            'hourly_cost_rate_snapshot' => 2000,

            'internal_cost_amount' => 16000,

            'cost_currency' => 'LKR',

            'costed_at' => now(),
        ]);

    ResourceCostRate::create([
        'user_id' => $user->id,

        'currency' => 'LKR',

        'hourly_cost_rate' => 3000,

        'effective_from' => '2026-07-01',

        'effective_to' => null,

        'active' => true,

        'created_by' => $admin->id,
    ]);

    $entry->refresh();

    expect(
        (float) $entry
            ->hourly_cost_rate_snapshot
    )->toBe(2000.0);

    expect(
        (float) $entry
            ->internal_cost_amount
    )->toBe(16000.0);
});
