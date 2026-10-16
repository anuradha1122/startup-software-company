<?php

namespace App\Notifications;

use App\Models\ChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ChangeRequestSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ChangeRequest $changeRequest
    ) {}

    public function via(
        object $notifiable
    ): array {
        return [
            'database',
        ];
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'change-request-submitted',

            'title' => 'Change request submitted',

            'message' => "{$this->changeRequest->change_request_number}: {$this->changeRequest->title}",

            'url' => "/admin/change-requests/{$this->changeRequest->public_id}",
        ];
    }
}
