<?php

namespace App\Notifications;

use App\Models\ProjectAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectAssignmentEndedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ProjectAssignment $assignment
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
                "Project assignment ended: {$this->assignment->project->project_number}"
            )
            ->line(
                'Your project assignment has ended.'
            )
            ->line(
                $this->assignment->project->name
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'project-assignment-ended',

            'title' => 'Project assignment ended',

            'message' => "Your assignment on {$this->assignment->project->project_number} has ended.",

            'url' => "/admin/projects/{$this->assignment->project->public_id}",
        ];
    }
}
