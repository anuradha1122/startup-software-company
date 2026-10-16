<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly LeaveRequest $leaveRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'leave-request-submitted',

            'title' => 'Leave request submitted',

            'message' => "{$this->leaveRequest->leave_number} requires review.",

            'url' => '/admin/hr/leave',
        ];
    }
}
