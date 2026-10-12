<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadFollowUpDueNotification extends Notification implements ShouldQueue
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
                "Lead follow-up due: {$this->lead->lead_number}"
            )
            ->line(
                'A lead requires follow-up.'
            )
            ->line(
                $this->lead->title
            )
            ->line(
                'Due: '
                .$this->lead
                    ->next_follow_up_at
                    ?->format('Y-m-d H:i')
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
            'type' => 'lead-follow-up-due',
            'title' => 'Lead follow-up due',
            'message' => "{$this->lead->lead_number} requires follow-up.",
            'url' => "/admin/leads/{$this->lead->public_id}",
        ];
    }
}
