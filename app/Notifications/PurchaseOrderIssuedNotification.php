<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PurchaseOrderIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PurchaseOrder $order
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
            'title' => 'Purchase order issued',

            'message' => $this->order
                ->purchase_order_number
                .' has been issued.',

            'url' => route(
                'admin.purchase-orders.show',
                $this->order
            ),
        ];
    }
}
