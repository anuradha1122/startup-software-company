<?php

namespace App\Notifications;

use App\Models\ProjectAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectAssignmentCreatedNotification extends Notification implements ShouldQueue
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
                "Project assignment: {$this->assignment->project->project_number}"
            )
            ->line(
                'You have been assigned to a project.'
            )
            ->line(
                "Project: {$this->assignment->project->name}"
            )
            ->line(
                "Role: {$this->assignment->role}"
            )
            ->line(
                "Allocation: {$this->assignment->allocation_percent}%"
            )
            ->action(
                'Open Project',
                url(
                    "/admin/projects/{$this->assignment->project->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'project-assignment-created',

            'title' => 'Project assignment',

            'message' => "You were assigned to {$this->assignment->project->project_number} as {$this->assignment->role}.",

            'url' => "/admin/projects/{$this->assignment->project->public_id}",
        ];
    }
}
