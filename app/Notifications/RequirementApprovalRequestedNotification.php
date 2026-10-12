<?php

namespace App\Notifications;

use App\Models\Requirement;
use App\Models\RequirementVersion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequirementApprovalRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Requirement $requirement,
        private readonly RequirementVersion $version
    ) {}

    public function via(object $notifiable): array
    {
        return [
            'mail',
        ];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        return (new MailMessage)
            ->subject(
                "Requirement approval requested: {$this->requirement->requirement_number}"
            )
            ->greeting('Hello,')
            ->line(
                'Your review and approval is requested for the following software requirements.'
            )
            ->line(
                $this->requirement->title
            )
            ->line(
                "Version {$this->version->version_number}"
            )
            ->line(
                'Please contact your project manager with your approval or requested revisions.'
            );
    }
}
