<?php

use App\Models\ClientPortalUser;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not expose internal support notes to portal user', function (): void {
    $project =
        Project::factory()->create();

    $internal =
        User::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $project->client_id,

            'name' => 'Portal User',

            'email' => 'portal-m14@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    $ticket =
        SupportTicket::create([
            'ticket_number' => 'SUP-000001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'client_portal_user_id' => $portalUser->id,

            'subject' => 'Issue',

            'description' => 'Issue',

            'priority' => 'medium',

            'severity' => 'normal',

            'status' => 'open',
        ]);

    $ticket
        ->messages()
        ->create([
            'author_type' => 'internal',

            'user_id' => $internal->id,

            'message' => 'CONFIDENTIAL-INTERNAL-NOTE',

            'internal_only' => true,
        ]);

    $ticket
        ->messages()
        ->create([
            'author_type' => 'internal',

            'user_id' => $internal->id,

            'message' => 'Client safe reply',

            'internal_only' => false,
        ]);

    $this
        ->actingAs(
            $portalUser,
            'client_portal'
        )
        ->get(
            "/portal/support/{$ticket->public_id}"
        )
        ->assertOk()
        ->assertDontSee(
            'CONFIDENTIAL-INTERNAL-NOTE'
        )
        ->assertSee(
            'Client safe reply'
        );
});
