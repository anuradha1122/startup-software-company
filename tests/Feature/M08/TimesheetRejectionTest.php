<?php

use App\Models\Project;
use App\Models\TaskWorkflowStatus;
use App\Models\Timesheet;
use App\Models\User;
use App\Notifications\TimesheetRejectedNotification;
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

it('rejects submitted timesheet and allows owner to edit again', function (): void {
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

            'title' => 'Rejected timesheet task',

            'assignee_id' => $user->id,

            'created_by' => $admin->id,
        ]);

    $weekStart = today()
        ->startOfWeek();

    $timesheet = Timesheet::create([
        'user_id' => $user->id,

        'week_start' => $weekStart,

        'week_end' => $weekStart
            ->copy()
            ->endOfWeek(),

        'status' => 'submitted',

        'submitted_at' => now(),
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

            'hours' => 4,

            'description' => 'Initial entry',

            'billable' => true,
        ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/timesheets/{$timesheet->public_id}/review",
            [
                'decision' => 'rejected',

                'review_notes' => 'Please correct the entries.',
            ]
        )
        ->assertRedirect();

    $timesheet->refresh();

    expect(
        $timesheet->status
    )->toBe('rejected');

    expect(
        $timesheet->review_notes
    )->toBe(
        'Please correct the entries.'
    );

    Notification::assertSentTo(
        $user,
        TimesheetRejectedNotification::class
    );

    $this
        ->actingAs($user)
        ->post(
            "/admin/timesheets/{$timesheet->public_id}/entries",
            [
                'project_id' => $project->id,

                'project_task_id' => $task->id,

                'work_date' => $weekStart
                    ->copy()
                    ->addDays(2)
                    ->format('Y-m-d'),

                'hours' => 2,

                'description' => 'Corrected additional entry',

                'billable' => true,
            ]
        )
        ->assertRedirect();

    expect(
        $timesheet
            ->entries()
            ->count()
    )->toBe(2);
});
