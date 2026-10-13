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

it('blocks starting task with incomplete dependency', function (): void {
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

    $progress =
        TaskWorkflowStatus::where(
            'slug',
            'in-progress'
        )->firstOrFail();

    $dependency =
        $project
            ->tasks()
            ->create([
                'task_sequence' => 1,
                'task_number' => "{$project->project_number}-T0001",
                'workflow_status_id' => $backlog->id,
                'title' => 'Dependency',
                'created_by' => $user->id,
            ]);

    $task =
        $project
            ->tasks()
            ->create([
                'task_sequence' => 2,
                'task_number' => "{$project->project_number}-T0002",
                'workflow_status_id' => $backlog->id,
                'title' => 'Dependent task',
                'created_by' => $user->id,
            ]);

    $task
        ->dependencies()
        ->attach(
            $dependency->id
        );

    expect(
        fn () => app(
            TaskWorkflowService::class
        )->move(
            $task,
            $progress,
            $user
        )
    )->toThrow(
        RuntimeException::class,
        'incomplete dependencies'
    );
});
