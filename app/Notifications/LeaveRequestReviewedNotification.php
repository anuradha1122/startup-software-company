<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly LeaveRequest $leaveRequest
    ) {}

    public function via(object $notifiable): array
    {
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
                "Leave request {$this->leaveRequest->status}"
            )
            ->line(
                "Your leave request {$this->leaveRequest->leave_number} was {$this->leaveRequest->status}."
            )
            ->action(
                'View Leave',
                url('/employee/leave')
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'leave-request-reviewed',

            'title' => 'Leave request reviewed',

            'message' => "{$this->leaveRequest->leave_number} was {$this->leaveRequest->status}.",

            'url' => '/employee/leave',
        ];
    }
}
