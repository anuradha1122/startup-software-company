<?php

namespace App\Notifications;

use App\Models\Requirement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequirementRevisionRequiredNotification extends Notification implements ShouldQueue
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
                "Revision required: {$this->requirement->requirement_number}"
            )
            ->line(
                'The client requested revisions to the requirement specification.'
            )
            ->action(
                'Open Requirement',
                url(
                    "/admin/requirements/{$this->requirement->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'requirement-revision-required',

            'title' => 'Requirement revision required',

            'message' => "{$this->requirement->requirement_number} requires revision.",

            'url' => "/admin/requirements/{$this->requirement->public_id}",
        ];
    }
}
