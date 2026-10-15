<?php

use App\Models\ClientApprovalRequest;
use App\Models\ClientPortalUser;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ClientApprovalRespondedNotification;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();

    $this->seed(
        RolePermissionSeeder::class
    );
});

it('allows same client portal user to approve pending request', function (): void {
    $internalUser =
        User::factory()->create();

    $project =
        Project::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $project->client_id,

            'name' => 'Client Approver',

            'email' => 'approver@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',

            'activated_at' => now(),
        ]);

    $approval =
        ClientApprovalRequest::create([
            'approval_number' => 'APR-000001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'type' => 'deliverable',

            'title' => 'Release Approval',

            'summary' => 'Please approve the release.',

            'status' => 'pending',

            'requested_by' => $internalUser->id,

            'requested_at' => now(),
        ]);

    $this
        ->actingAs(
            $portalUser,
            'client_portal'
        )
        ->post(
            "/portal/approvals/{$approval->public_id}/respond",
            [
                'decision' => 'approved',

                'response_comment' => 'Approved.',
            ]
        )
        ->assertRedirect();

    $approval->refresh();

    expect(
        $approval->status
    )->toBe('approved');

    expect(
        $approval->responded_by
    )->toBe(
        $portalUser->id
    );

    expect(
        $approval->responded_at
    )->not->toBeNull();

    Notification::assertSentTo(
        $internalUser,
        ClientApprovalRespondedNotification::class
    );
});
