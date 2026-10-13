<?php

namespace App\Notifications;

use App\Models\WorkSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkSubmissionApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly WorkSubmission $submission
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
                "Work approved: {$this->submission->task->task_number}"
            )
            ->line(
                'Your work submission has been approved.'
            )
            ->action(
                'Open Task',
                url(
                    "/admin/tasks/{$this->submission->task->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'work-approved',

            'title' => 'Work approved',

            'message' => "{$this->submission->task->task_number} has been approved.",

            'url' => "/admin/tasks/{$this->submission->task->public_id}",
        ];
    }
}
