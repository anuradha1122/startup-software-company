<?php

namespace App\Notifications;

use App\Models\ProjectRisk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectRiskEscalatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ProjectRisk $risk
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
                "Project risk escalated: {$this->risk->risk_number}"
            )
            ->line(
                $this->risk->title
            )
            ->line(
                "Risk score: {$this->risk->score}"
            )
            ->action(
                'Open Project',
                url(
                    "/admin/projects/{$this->risk->project->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'project-risk-escalated',

            'title' => 'Project risk escalated',

            'message' => "{$this->risk->risk_number}: {$this->risk->title}",

            'url' => "/admin/projects/{$this->risk->project->public_id}",
        ];
    }
}
