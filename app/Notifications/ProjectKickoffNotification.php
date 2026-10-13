<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectKickoffNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Project $project
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
                "Project kickoff: {$this->project->project_number}"
            )
            ->greeting('Hello,')
            ->line(
                'Your project has now been formally initiated.'
            )
            ->line(
                $this->project->name
            )
            ->line(
                "Project Manager: {$this->project->projectManager->name}"
            )
            ->line(
                'We will keep you informed regarding key milestones and project governance.'
            );
    }
}
