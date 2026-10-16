<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly SupportTicket $ticket
    ) {}

    public function via(object $notifiable): array
    {
        return [
            'mail',
            'database',
        ];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        return (new MailMessage)
            ->subject(
                "Support ticket {$this->ticket->ticket_number}"
            )
            ->line(
                'A new support ticket has been created.'
            )
            ->line(
                $this->ticket->subject
            )
            ->action(
                'Open Ticket',
                url(
                    "/admin/support/{$this->ticket->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'support-ticket-created',

            'title' => 'Support ticket created',

            'message' => "{$this->ticket->ticket_number}: {$this->ticket->subject}",

            'url' => "/admin/support/{$this->ticket->public_id}",
        ];
    }
}
