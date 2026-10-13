<?php

namespace App\Notifications;

use App\Models\WorkSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkSubmittedNotification extends Notification implements ShouldQueue
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
                "Work submitted: {$this->submission->task->task_number}"
            )
            ->line(
                $this->submission->task->title
            )
            ->line(
                "Submission version {$this->submission->version_number} is ready for review."
            )
            ->action(
                'Review Work',
                url(
                    "/admin/tasks/{$this->submission->task->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'work-submitted',

            'title' => 'Work submitted',

            'message' => "{$this->submission->task->task_number} is ready for review.",

            'url' => "/admin/tasks/{$this->submission->task->public_id}",
        ];
    }
}
