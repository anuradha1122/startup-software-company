<?php

namespace App\Notifications;

use App\Models\Timesheet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimesheetRejectedNotification extends Notification implements ShouldQueue
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
                'Timesheet changes required'
            )
            ->line(
                'Your weekly timesheet requires changes.'
            )
            ->line(
                $this->timesheet->review_notes
                ?? 'Please review the submitted entries.'
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
            'type' => 'timesheet-rejected',

            'title' => 'Timesheet changes required',

            'message' => "Timesheet for {$this->timesheet->week_start->format('Y-m-d')} requires changes.",

            'url' => "/admin/timesheets/{$this->timesheet->public_id}",
        ];
    }
}
