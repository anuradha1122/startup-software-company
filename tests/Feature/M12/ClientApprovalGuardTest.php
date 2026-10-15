<?php

use App\Models\ClientApprovalRequest;
use App\Models\ClientPortalUser;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not allow another client to answer an approval', function (): void {
    $projectA =
        Project::factory()->create();

    $projectB =
        Project::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $projectB->client_id,

            'name' => 'Wrong Client',

            'email' => 'wrong@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    $approval =
        ClientApprovalRequest::create([
            'approval_number' => 'APR-000001',

            'client_id' => $projectA->client_id,

            'project_id' => $projectA->id,

            'type' => 'milestone',

            'title' => 'Milestone approval',

            'status' => 'pending',

            'requested_at' => now(),
        ]);

    $this
        ->actingAs(
            $portalUser,
            'client_portal'
        )
        ->get(
            "/portal/approvals/{$approval->public_id}"
        )
        ->assertNotFound();
});
