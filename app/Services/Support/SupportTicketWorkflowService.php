<?php

namespace App\Services\Support;

use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketAssignedNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SupportTicketWorkflowService
{
    public function assign(
        SupportTicket $ticket,
        User $assignee
    ): SupportTicket {
        return DB::transaction(
            function () use (
                $ticket,
                $assignee
            ): SupportTicket {
                $ticket =
                    SupportTicket::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $ticket->id
                        );

                if ($ticket->isClosed()) {
                    throw new RuntimeException(
                        'Closed tickets cannot be assigned.'
                    );
                }

                if (
                    $assignee->status
                    !== 'active'
                ) {
                    throw new RuntimeException(
                        'Only active users may receive support tickets.'
                    );
                }

                $ticket->update([
                    'assigned_to' => $assignee->id,

                    'status' => $ticket->status === 'open'
                            ? 'assigned'
                            : $ticket->status,
                ]);

                $assignee->notify(
                    new SupportTicketAssignedNotification(
                        $ticket
                    )
                );

                return $ticket->refresh();
            }
        );
    }

    public function transition(
        SupportTicket $ticket,
        string $status
    ): SupportTicket {
        $allowed = [
            'open' => [
                'assigned',
                'in_progress',
                'cancelled',
            ],

            'assigned' => [
                'in_progress',
                'waiting_client',
                'resolved',
                'cancelled',
            ],

            'in_progress' => [
                'waiting_client',
                'resolved',
                'cancelled',
            ],

            'waiting_client' => [
                'in_progress',
                'resolved',
                'cancelled',
            ],

            'resolved' => [
                'in_progress',
                'closed',
            ],

            'closed' => [],

            'cancelled' => [],
        ];

        if (
            ! in_array(
                $status,
                $allowed[$ticket->status]
                ?? [],
                true
            )
        ) {
            throw new RuntimeException(
                "Invalid support ticket transition from {$ticket->status} to {$status}."
            );
        }

        $attributes = [
            'status' => $status,
        ];

        if ($status === 'resolved') {
            $attributes[
                'resolved_at'
            ] = now();
        }

        if ($status === 'closed') {
            $attributes[
                'closed_at'
            ] = now();
        }

        if (
            $status === 'in_progress'
            && $ticket->status
            === 'resolved'
        ) {
            $attributes[
                'resolved_at'
            ] = null;

            $attributes[
                'closed_at'
            ] = null;
        }

        $ticket->update(
            $attributes
        );

        return $ticket->refresh();
    }
}
