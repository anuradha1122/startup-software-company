<?php

use App\Models\ClientPortalUser;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\Support\SupportTicketMessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('records first response only for first client visible internal reply', function (): void {
    $project =
        Project::factory()->create();

    $internalUser =
        User::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $project->client_id,

            'name' => 'Client User',

            'email' => 'm14-reply@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    $ticket =
        SupportTicket::create([
            'ticket_number' => 'SUP-000001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'client_portal_user_id' => $portalUser->id,

            'subject' => 'Support issue',

            'description' => 'Support issue',

            'priority' => 'medium',

            'severity' => 'normal',

            'status' => 'in_progress',

            'first_response_due_at' => now()->addHours(8),

            'resolution_due_at' => now()->addHours(72),
        ]);

    $service =
        app(
            SupportTicketMessageService::class
        );

    $service->internalReply(
        $ticket,
        $internalUser,
        'Internal investigation note.',
        true
    );

    expect(
        $ticket
            ->fresh()
            ->first_responded_at
    )->toBeNull();

    $service->internalReply(
        $ticket->fresh(),
        $internalUser,
        'We are working on your issue.',
        false
    );

    expect(
        $ticket
            ->fresh()
            ->first_responded_at
    )->not->toBeNull();

    $firstResponse =
        $ticket
            ->fresh()
            ->first_responded_at;

    $service->internalReply(
        $ticket->fresh(),
        $internalUser,
        'Another public reply.',
        false
    );

    expect(
        $ticket
            ->fresh()
            ->first_responded_at
            ->equalTo(
                $firstResponse
            )
    )->toBeTrue();
});

it('moves waiting client ticket back to in progress after client reply', function (): void {
    $project =
        Project::factory()->create();

    $portalUser =
        ClientPortalUser::create([
            'client_id' => $project->client_id,

            'name' => 'Client User',

            'email' => 'm14-client-reply@example.com',

            'password' => bcrypt('password'),

            'status' => 'active',
        ]);

    $ticket =
        SupportTicket::create([
            'ticket_number' => 'SUP-000002',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'client_portal_user_id' => $portalUser->id,

            'subject' => 'Waiting client',

            'description' => 'Waiting for client information.',

            'priority' => 'medium',

            'severity' => 'normal',

            'status' => 'waiting_client',
        ]);

    app(
        SupportTicketMessageService::class
    )->clientReply(
        $ticket,
        $portalUser,
        'Here is the requested information.'
    );

    expect(
        $ticket
            ->fresh()
            ->status
    )->toBe('in_progress');
});
