<?php

namespace App\Notifications;

use App\Models\Project;
use App\Services\Finance\ProjectFinancialResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectMarginAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Project $project,
        private readonly ProjectFinancialResult $financials,
        private readonly float $warningMargin
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
                "Margin warning: {$this->project->project_number}"
            )
            ->line(
                'A project has fallen below its configured margin warning threshold.'
            )
            ->line(
                "Project: {$this->project->project_number} — {$this->project->name}"
            )
            ->line(
                'Current margin: '
                .number_format(
                    (float) $this
                        ->financials
                        ->grossMarginPercent,
                    2
                )
                .'%'
            )
            ->line(
                'Warning threshold: '
                .number_format(
                    $this->warningMargin,
                    2
                )
                .'%'
            )
            ->action(
                'View Profitability',
                url(
                    "/admin/projects/{$this->project->public_id}/profitability"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'project-margin-warning',

            'title' => 'Project margin warning',

            'message' => "{$this->project->project_number} is below its margin warning threshold.",

            'project_public_id' => $this->project->public_id,

            'gross_margin_percent' => $this
                ->financials
                ->grossMarginPercent,

            'warning_margin_percent' => $this->warningMargin,

            'url' => "/admin/projects/{$this->project->public_id}/profitability",
        ];
    }
}
