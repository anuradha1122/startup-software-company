<?php

namespace App\Notifications;

use App\Models\Timesheet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimesheetApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Timesheet $timesheet
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
                'Timesheet approved'
            )
            ->line(
                'Your weekly timesheet has been approved.'
            )
            ->line(
                "Week starting {$this->timesheet->week_start->format('Y-m-d')}."
            )
            ->action(
                'Open Timesheet',
                url(
                    "/admin/timesheets/{$this->timesheet->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'timesheet-approved',

            'title' => 'Timesheet approved',

            'message' => "Timesheet for {$this->timesheet->week_start->format('Y-m-d')} was approved.",

            'url' => "/admin/timesheets/{$this->timesheet->public_id}",
        ];
    }
}
