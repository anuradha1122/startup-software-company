<?php

namespace App\Notifications;

use App\Models\ProjectTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ProjectTask $task
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
                "Task assigned: {$this->task->task_number}"
            )
            ->line(
                $this->task->title
            )
            ->line(
                "Project: {$this->task->project->name}"
            )
            ->action(
                'Open Task',
                url(
                    "/admin/tasks/{$this->task->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'task-assigned',

            'title' => 'Task assigned',

            'message' => "{$this->task->task_number}: {$this->task->title}",

            'url' => "/admin/tasks/{$this->task->public_id}",
        ];
    }
}
