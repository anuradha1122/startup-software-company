<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentRecordedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Payment $payment
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
            'type' => 'payment-recorded',

            'title' => 'Payment recorded',

            'message' => "{$this->payment->payment_number} recorded for {$this->payment->currency} {$this->payment->amount}.",

            'url' => "/admin/payments/{$this->payment->public_id}",
        ];
    }
}
