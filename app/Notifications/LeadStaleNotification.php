<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadStaleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Lead $lead
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        return (new MailMessage)
            ->subject(
                "Stale lead: {$this->lead->lead_number}"
            )
            ->line(
                'This lead has had no recent activity.'
            )
            ->line($this->lead->title)
            ->action(
                'Review Lead',
                url(
                    "/admin/leads/{$this->lead->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'lead-stale',
            'title' => 'Stale lead',
            'message' => "{$this->lead->lead_number} has no recent activity.",
            'url' => "/admin/leads/{$this->lead->public_id}",
        ];
    }
}
