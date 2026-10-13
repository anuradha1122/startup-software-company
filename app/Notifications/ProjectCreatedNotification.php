<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Project $project
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
                "Project created: {$this->project->project_number}"
            )
            ->line(
                'A new project has been created from a signed contract.'
            )
            ->line(
                $this->project->name
            )
            ->action(
                'Open Project',
                url(
                    "/admin/projects/{$this->project->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'project-created',

            'title' => 'Project created',

            'message' => "{$this->project->project_number} has been created.",

            'url' => "/admin/projects/{$this->project->public_id}",
        ];
    }
}
