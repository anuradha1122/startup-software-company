<?php

use App\Models\Project;
use App\Models\TaskWorkflowStatus;
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

it('approves work submission and completes task', function (): void {
    $admin =
        User::factory()->create();

    $admin->assignRole('Admin');

    $developer =
        User::factory()->create();

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $review =
        TaskWorkflowStatus::where(
            'slug',
            'review'
        )->firstOrFail();

    $task =
        $project
            ->tasks()
            ->create([
                'task_sequence' => 1,

                'task_number' => "{$project->project_number}-T0001",

                'workflow_status_id' => $review->id,

                'title' => 'Review task',

                'assignee_id' => $developer->id,

                'created_by' => $admin->id,
            ]);

    $submission =
        $task
            ->submissions()
            ->create([
                'version_number' => 1,

                'status' => 'submitted',

                'summary' => 'Completed work.',

                'submitted_by' => $developer->id,

                'submitted_at' => now(),
            ]);

    $this
        ->actingAs($admin)
        ->post(
            "/admin/work-submissions/{$submission->public_id}/review",
            [
                'decision' => 'approved',

                'review_notes' => 'Approved.',
            ]
        )
        ->assertRedirect();

    expect(
        $submission
            ->fresh()
            ->status
    )->toBe('approved');

    expect(
        $task
            ->fresh()
            ->workflowStatus
            ->slug
    )->toBe('done');

    expect(
        $task
            ->fresh()
            ->completed_at
    )->not->toBeNull();
});
