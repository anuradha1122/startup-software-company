<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChangeRequestDecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ChangeRequest $changeRequest
    ) {}

    public function via(
        object $notifiable
    ): array {
        return [
            'mail',
        ];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        return (new MailMessage)
            ->subject(
                "Change request {$this->changeRequest->status}: {$this->changeRequest->change_request_number}"
            )
            ->line(
                'Your change request has been reviewed.'
            )
            ->line(
                "Decision: {$this->changeRequest->status}"
            )
            ->action(
                'View Change Request',
                route(
                    'portal.change-requests.show',
                    $this->changeRequest
                )
            );
    }
}
