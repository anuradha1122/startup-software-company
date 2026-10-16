<?php

use App\Models\ChangeRequest;
use App\Models\ClientPortalUser;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not expose internal change request costing to portal', function (): void {
    $project =
        Project::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $project->client_id,

            'name' => 'Portal User',

            'email' => 'portal-change@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    $changeRequest =
        ChangeRequest::create([
            'change_request_number' => 'CR-000001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'client_portal_user_id' => $portalUser->id,

            'title' => 'Portal Change',

            'description' => 'Change',

            'internal_analysis' => 'CONFIDENTIAL-INTERNAL-ANALYSIS',

            'priority' => 'medium',

            'status' => 'under_review',

            'submitted_at' => now(),
        ]);

    $changeRequest
        ->estimate()
        ->create([
            'currency' => 'LKR',

            'client_amount' => 50000,

            'internal_estimated_cost' => 12345,

            'estimated_hours' => 20,

            'estimated_days' => 3,

            'client_notes' => 'Client safe estimate.',

            'internal_notes' => 'CONFIDENTIAL-INTERNAL-COST-NOTE',
        ]);

    $response =
        $this
            ->actingAs(
                $portalUser,
                'client_portal'
            )
            ->get(
                "/portal/change-requests/{$changeRequest->public_id}"
            );

    $response
        ->assertOk()
        ->assertDontSee(
            'CONFIDENTIAL-INTERNAL-ANALYSIS'
        )
        ->assertDontSee(
            'CONFIDENTIAL-INTERNAL-COST-NOTE'
        )
        ->assertDontSee(
            '12345'
        )
        ->assertSee(
            'Client safe estimate'
        );
});
