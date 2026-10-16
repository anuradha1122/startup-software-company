<?php

use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\Support\SupportTicketWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('assigns active user to support ticket', function (): void {
    $project =
        Project::factory()->create();

    $assignee =
        User::factory()->create([
            'status' => 'active',
        ]);

    $ticket =
        SupportTicket::create([
            'ticket_number' => 'SUP-000001',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'subject' => 'Support issue',

            'description' => 'Issue',

            'priority' => 'medium',

            'severity' => 'normal',

            'status' => 'open',
        ]);

    app(
        SupportTicketWorkflowService::class
    )->assign(
        $ticket,
        $assignee
    );

    expect(
        $ticket
            ->fresh()
            ->assigned_to
    )->toBe(
        $assignee->id
    );

    expect(
        $ticket
            ->fresh()
            ->status
    )->toBe('assigned');
});
