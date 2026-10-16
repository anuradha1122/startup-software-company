<?php

use App\Models\ClientPortalUser;
use App\Models\Project;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('blocks portal access to another client support ticket', function (): void {
    $projectA =
        Project::factory()->create();

    $projectB =
        Project::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $projectA->client_id,

            'name' => 'Client A',

            'email' => 'client-a-m14@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    $ticket =
        SupportTicket::create([
            'ticket_number' => 'SUP-000001',

            'client_id' => $projectB->client_id,

            'project_id' => $projectB->id,

            'subject' => 'Other client issue',

            'description' => 'Private',

            'priority' => 'medium',

            'severity' => 'normal',

            'status' => 'open',
        ]);

    $this
        ->actingAs(
            $portalUser,
            'client_portal'
        )
        ->get(
            "/portal/support/{$ticket->public_id}"
        )
        ->assertNotFound();
});
