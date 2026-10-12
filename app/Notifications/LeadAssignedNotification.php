<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Lead $lead
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
                "Lead assigned: {$this->lead->lead_number}"
            )
            ->greeting(
                "Hello {$notifiable->name},"
            )
            ->line(
                'A CRM lead has been assigned to you.'
            )
            ->line(
                "{$this->lead->lead_number} — {$this->lead->title}"
            )
            ->action(
                'Open Lead',
                url(
                    "/admin/leads/{$this->lead->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'lead-assigned',
            'title' => 'Lead assigned',
            'message' => "{$this->lead->lead_number} — {$this->lead->title}",
            'url' => "/admin/leads/{$this->lead->public_id}",
        ];
    }
}
