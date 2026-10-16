<?php

namespace App\Services\Support;

use App\Models\ClientPortalUser;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketCreatedNotification;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\Notification;

class SupportTicketService
{
    public function __construct(
        private readonly DocumentNumberService $numbers,
        private readonly SupportSlaService $sla
    ) {}

    public function createInternal(
        array $data,
        User $creator
    ): SupportTicket {
        return $this->create(
            data: $data,
            clientPortalUser: null,
            internalCreator: $creator
        );
    }

    public function createFromPortal(
        array $data,
        ClientPortalUser $portalUser
    ): SupportTicket {
        return $this->create(
            data: [
                ...$data,
                'client_id' => $portalUser->client_id,
            ],
            clientPortalUser: $portalUser,
            internalCreator: null
        );
    }

    private function create(
        array $data,
        ?ClientPortalUser $clientPortalUser,
        ?User $internalCreator
    ): SupportTicket {
        $deadlines =
            $this->sla->deadlines(
                $data['priority'],
                $data['severity']
            );

        $ticket =
            SupportTicket::create([
                'ticket_number' => $this->numbers->next(
                    'support_ticket'
                ),

                'client_id' => $data['client_id'],

                'project_id' => $data['project_id']
                    ?? null,

                'client_portal_user_id' => $clientPortalUser?->id,

                'subject' => $data['subject'],

                'description' => $data['description'],

                'priority' => $data['priority'],

                'severity' => $data['severity'],

                'status' => 'open',

                'first_response_due_at' => $deadlines[
                        'first_response_due_at'
                    ],

                'resolution_due_at' => $deadlines[
                        'resolution_due_at'
                    ],

                'created_by' => $internalCreator?->id,
            ]);

        $recipients =
            User::query()
                ->permission(
                    'support-tickets.view'
                )
                ->where(
                    'status',
                    'active'
                )
                ->get();

        Notification::send(
            $recipients,
            new SupportTicketCreatedNotification(
                $ticket
            )
        );

        return $ticket;
    }
}
