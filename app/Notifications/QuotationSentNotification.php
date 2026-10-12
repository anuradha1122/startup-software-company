<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Quotation $quotation
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        return (new MailMessage)
            ->subject(
                "Quotation {$this->quotation->quotation_number}"
            )
            ->line(
                'Your quotation is ready for review.'
            )
            ->line(
                "{$this->quotation->currency} {$this->quotation->total_amount}"
            )
            ->line(
                "Valid until {$this->quotation->valid_until?->format('Y-m-d')}."
            );
    }
}
