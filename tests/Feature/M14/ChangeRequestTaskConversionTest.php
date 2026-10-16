<?php

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\TaskWorkflowStatus;
use App\Models\User;
use App\Services\ChangeRequests\ChangeRequestTaskService;
use Database\Seeders\TaskWorkflowStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(
        TaskWorkflowStatusSeeder::class
    );
});

it('creates M06 delivery task from approved change request', function (): void {
    $user =
        User::factory()->create();

    $project =
        Project::factory()->create();

    $changeRequest =
        ChangeRequest::create([
            'change_request_number' => 'CR-000001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'title' => 'Approved change',

            'description' => 'Build approved change.',

            'priority' => 'high',

            'status' => 'approved',

            'approved_at' => now(),
        ]);

    $task =
        app(
            ChangeRequestTaskService::class
        )->createTask(
            $changeRequest,
            $user
        );

    expect(
        $task->project_id
    )->toBe(
        $project->id
    );

    expect(
        $task->title
    )->toContain(
        'CR-000001'
    );

    expect(
        TaskWorkflowStatus::find(
            $task->workflow_status_id
        )?->slug
    )->toBe('backlog');
});
