<?php

namespace App\Notifications;

use App\Models\Timesheet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimesheetSubmittedNotification extends Notification implements ShouldQueue
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
                "Timesheet submitted: {$this->timesheet->user->name}"
            )
            ->line(
                'A weekly timesheet is ready for review.'
            )
            ->line(
                "Week: {$this->timesheet->week_start->format('Y-m-d')} to {$this->timesheet->week_end->format('Y-m-d')}"
            )
            ->action(
                'Review Timesheet',
                url(
                    "/admin/timesheets/{$this->timesheet->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'timesheet-submitted',

            'title' => 'Timesheet submitted',

            'message' => "{$this->timesheet->user->name} submitted a timesheet.",

            'url' => "/admin/timesheets/{$this->timesheet->public_id}",
        ];
    }
}
