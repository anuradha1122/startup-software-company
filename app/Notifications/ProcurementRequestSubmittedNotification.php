<?php

namespace App\Notifications;

use App\Models\ProcurementRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ProcurementRequestSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ProcurementRequest $request
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return [
            'database',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Procurement request submitted',

            'message' => $this->request
                ->request_number
                .' requires review.',

            'url' => route(
                'admin.procurement.requests.show',
                $this->request
            ),
        ];
    }
}
