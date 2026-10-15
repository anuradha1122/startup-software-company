<?php

namespace App\Notifications;

use App\Models\ClientApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientApprovalRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ClientApprovalRequest $approval
    ) {}

    public function via(
        object $notifiable
    ): array {
        return ['mail'];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        return (new MailMessage)
            ->subject(
                "Approval requested: {$this->approval->approval_number}"
            )
            ->line(
                'A new item requires your approval.'
            )
            ->line(
                $this->approval->title
            )
            ->action(
                'Review Approval',
                route(
                    'portal.approvals.show',
                    $this->approval
                )
            );
    }
}
