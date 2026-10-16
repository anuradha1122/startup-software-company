<?php

use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\Support\SupportTicketMessageService;
use App\Services\Support\SupportTicketWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

it('rejects inactive support assignee', function (): void {
    $project =
        Project::factory()->create();

    $inactiveUser =
        User::factory()->create([
            'status' => 'inactive',
        ]);

    $ticket =
        SupportTicket::create([
            'ticket_number' => 'SUP-000003',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'subject' => 'Issue',

            'description' => 'Issue',

            'priority' => 'medium',

            'severity' => 'normal',

            'status' => 'open',
        ]);

    expect(
        fn () => app(
            SupportTicketWorkflowService::class
        )->assign(
            $ticket,
            $inactiveUser
        )
    )->toThrow(
        RuntimeException::class,
        'active'
    );
});

it('rejects invalid support ticket transition', function (): void {
    $project =
        Project::factory()->create();

    $ticket =
        SupportTicket::create([
            'ticket_number' => 'SUP-000004',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'subject' => 'Issue',

            'description' => 'Issue',

            'priority' => 'medium',

            'severity' => 'normal',

            'status' => 'open',
        ]);

    expect(
        fn () => app(
            SupportTicketWorkflowService::class
        )->transition(
            $ticket,
            'closed'
        )
    )->toThrow(
        RuntimeException::class,
        'Invalid support ticket transition'
    );
});

it('rejects replies to closed ticket', function (): void {
    $project =
        Project::factory()->create();

    $user =
        User::factory()->create();

    $ticket =
        SupportTicket::create([
            'ticket_number' => 'SUP-000005',

            'client_id' => $project->client_id,

            'project_id' => $project->id,

            'subject' => 'Closed issue',

            'description' => 'Closed issue',

            'priority' => 'medium',

            'severity' => 'normal',

            'status' => 'closed',

            'closed_at' => now(),
        ]);

    expect(
        fn () => app(
            SupportTicketMessageService::class
        )->internalReply(
            $ticket,
            $user,
            'Should not be allowed.',
            false
        )
    )->toThrow(
        HttpException::class
    );
});
