<?php

namespace App\Notifications;

use App\Models\ClientPortalUser;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly SupportTicket $ticket,
        private readonly SupportTicketMessage $message
    ) {}

    public function via(
        object $notifiable
    ): array {
        return [
            'mail',
            'database',
        ];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        $portal =
            $notifiable instanceof ClientPortalUser;

        $url =
            $portal
                ? route(
                    'portal.support.show',
                    $this->ticket
                )
                : url(
                    "/admin/support/{$this->ticket->public_id}"
                );

        return (new MailMessage)
            ->subject(
                "Support update {$this->ticket->ticket_number}"
            )
            ->line(
                'A new reply was added to the support ticket.'
            )
            ->action(
                'Open Ticket',
                $url
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'support-ticket-reply',

            'title' => 'Support ticket reply',

            'message' => "{$this->ticket->ticket_number} has a new reply.",

            'url' => "/admin/support/{$this->ticket->public_id}",
        ];
    }
}
