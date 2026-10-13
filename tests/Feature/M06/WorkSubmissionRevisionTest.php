<?php

use App\Models\Project;
use App\Models\TaskWorkflowStatus;
use App\Models\User;
use App\Services\Tasks\WorkSubmissionService;
use Database\Seeders\TaskWorkflowStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        TaskWorkflowStatusSeeder::class
    );
});

it('creates a new work submission version after changes are required', function (): void {
    $user =
        User::factory()->create();

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $changes =
        TaskWorkflowStatus::where(
            'slug',
            'changes-required'
        )->firstOrFail();

    $task =
        $project
            ->tasks()
            ->create([
                'task_sequence' => 1,

                'task_number' => "{$project->project_number}-T0001",

                'workflow_status_id' => $changes->id,

                'title' => 'Revision task',

                'assignee_id' => $user->id,

                'created_by' => $user->id,
            ]);

    $task
        ->submissions()
        ->create([
            'version_number' => 1,

            'status' => 'changes_required',

            'summary' => 'First version',

            'submitted_by' => $user->id,

            'submitted_at' => now(),
        ]);

    $submission =
        app(
            WorkSubmissionService::class
        )->submit(
            $task,
            [
                'summary' => 'Second version',

                'testing_notes' => 'Retested',
            ],
            $user
        );

    expect(
        $submission->version_number
    )->toBe(2);

    expect(
        $task
            ->submissions()
            ->where(
                'version_number',
                1
            )
            ->firstOrFail()
            ->status
    )->toBe('superseded');
});
