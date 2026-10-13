<?php

use App\Models\Project;
use App\Models\TaskWorkflowStatus;
use App\Models\User;
use App\Services\Tasks\TaskWorkflowService;
use Database\Seeders\TaskWorkflowStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        TaskWorkflowStatusSeeder::class
    );
});

it('does not allow task completion without approved submission', function (): void {
    $user =
        User::factory()->create();

    $project =
        Project::factory()->create([
            'status' => 'active',
        ]);

    $backlog =
        TaskWorkflowStatus::where(
            'slug',
            'backlog'
        )->firstOrFail();

    $done =
        TaskWorkflowStatus::where(
            'slug',
            'done'
        )->firstOrFail();

    $task =
        $project
            ->tasks()
            ->create([
                'task_sequence' => 1,

                'task_number' => "{$project->project_number}-T0001",

                'workflow_status_id' => $backlog->id,

                'title' => 'Incomplete task',

                'created_by' => $user->id,
            ]);

    expect(
        fn () => app(
            TaskWorkflowService::class
        )->move(
            $task,
            $done,
            $user
        )
    )->toThrow(
        RuntimeException::class,
        'work submission'
    );
});
