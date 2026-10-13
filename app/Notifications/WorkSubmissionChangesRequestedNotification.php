<?php

namespace App\Notifications;

use App\Models\WorkSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkSubmissionChangesRequestedNotification extends Notification implements ShouldQueue
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
                "Changes required: {$this->submission->task->task_number}"
            )
            ->line(
                'Changes have been requested for your submitted work.'
            )
            ->line(
                $this->submission->review_notes
                ?? 'Review the task for details.'
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
            'type' => 'work-changes-required',

            'title' => 'Changes required',

            'message' => "{$this->submission->task->task_number} requires changes.",

            'url' => "/admin/tasks/{$this->submission->task->public_id}",
        ];
    }
}
