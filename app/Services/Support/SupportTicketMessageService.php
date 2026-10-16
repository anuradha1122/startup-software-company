<?php

namespace App\Services\Support;

use App\Models\ClientPortalUser;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use App\Notifications\SupportTicketRepliedNotification;
use Illuminate\Support\Facades\DB;

class SupportTicketMessageService
{
    public function internalReply(
        SupportTicket $ticket,
        User $user,
        string $message,
        bool $internalOnly
    ): SupportTicketMessage {
        return DB::transaction(
            function () use (
                $ticket,
                $user,
                $message,
                $internalOnly
            ): SupportTicketMessage {
                $ticket =
                    SupportTicket::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $ticket->id
                        );

                abort_if(
                    $ticket->isClosed(),
                    409,
                    'Closed tickets cannot receive replies.'
                );

                $reply =
                    $ticket
                        ->messages()
                        ->create([
                            'author_type' => 'internal',

                            'user_id' => $user->id,

                            'message' => $message,

                            'internal_only' => $internalOnly,
                        ]);

                if (
                    ! $ticket->first_responded_at
                    && ! $internalOnly
                ) {
                    $ticket->update([
                        'first_responded_at' => now(),
                    ]);
                }

                if (
                    ! $internalOnly
                    && $ticket->portalUser
                ) {
                    $ticket
                        ->portalUser
                        ->notify(
                            new SupportTicketRepliedNotification(
                                $ticket,
                                $reply
                            )
                        );
                }

                return $reply;
            }
        );
    }

    public function clientReply(
        SupportTicket $ticket,
        ClientPortalUser $portalUser,
        string $message
    ): SupportTicketMessage {
        return DB::transaction(
            function () use (
                $ticket,
                $portalUser,
                $message
            ): SupportTicketMessage {
                $ticket =
                    SupportTicket::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $ticket->id
                        );

                abort_unless(
                    $ticket->client_id
                    === $portalUser->client_id,
                    404
                );

                abort_if(
                    $ticket->isClosed(),
                    409
                );

                $reply =
                    $ticket
                        ->messages()
                        ->create([
                            'author_type' => 'client',

                            'client_portal_user_id' => $portalUser->id,

                            'message' => $message,

                            'internal_only' => false,
                        ]);

                if (
                    $ticket->status
                    === 'waiting_client'
                ) {
                    $ticket->update([
                        'status' => 'in_progress',
                    ]);
                }

                if ($ticket->assignee) {
                    $ticket
                        ->assignee
                        ->notify(
                            new SupportTicketRepliedNotification(
                                $ticket,
                                $reply
                            )
                        );
                }

                return $reply;
            }
        );
    }
}
