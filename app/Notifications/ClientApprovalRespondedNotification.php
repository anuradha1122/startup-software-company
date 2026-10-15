<?php

namespace App\Notifications;

use App\Models\ClientApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientApprovalRespondedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ClientApprovalRequest $approval
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
        return (new MailMessage)
            ->subject(
                "Client response: {$this->approval->approval_number}"
            )
            ->line(
                'A client approval request has been answered.'
            )
            ->line(
                "Decision: {$this->approval->status}"
            )
            ->line(
                "Responded by: {$this->approval->responder?->name}"
            )
            ->action(
                'Open Project',
                url(
                    "/admin/projects/{$this->approval->project->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'client-approval-response',

            'title' => 'Client approval response',

            'message' => "{$this->approval->approval_number} was {$this->approval->status}.",

            'url' => "/admin/projects/{$this->approval->project->public_id}/client-approvals",
        ];
    }
}
