<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SupportTicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly SupportTicket $ticket
    ) {}

    public function via(
        object $notifiable
    ): array {
        return [
            'database',
        ];
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'support-ticket-assigned',

            'title' => 'Support ticket assigned',

            'message' => "{$this->ticket->ticket_number} was assigned to you.",

            'url' => "/admin/support/{$this->ticket->public_id}",
        ];
    }
}
