<?php

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ChangeRequests\ChangeRequestReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('requires estimate before approving change request', function (): void {
    $reviewer =
        User::factory()->create();

    $project =
        Project::factory()->create();

    $changeRequest =
        ChangeRequest::create([
            'change_request_number' => 'CR-000001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'title' => 'Change',

            'description' => 'Change',

            'priority' => 'medium',

            'status' => 'submitted',

            'submitted_at' => now(),
        ]);

    expect(
        fn () => app(
            ChangeRequestReviewService::class
        )->decide(
            $changeRequest,
            $reviewer,
            'approved',
            null
        )
    )->toThrow(
        RuntimeException::class,
        'estimate'
    );
});
