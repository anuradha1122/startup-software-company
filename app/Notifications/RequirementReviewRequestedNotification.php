<?php

namespace App\Notifications;

use App\Models\Requirement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequirementReviewRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Requirement $requirement
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
                "Requirement review: {$this->requirement->requirement_number}"
            )
            ->line(
                'A requirement specification is ready for review.'
            )
            ->line(
                $this->requirement->title
            )
            ->action(
                'Review Requirement',
                url(
                    "/admin/requirements/{$this->requirement->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'requirement-review',

            'title' => 'Requirement review requested',

            'message' => "{$this->requirement->requirement_number} is ready for review.",

            'url' => "/admin/requirements/{$this->requirement->public_id}",
        ];
    }
}
