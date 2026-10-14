<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceIssuedInternalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Invoice $invoice
    ) {}

    public function via(
        object $notifiable
    ): array {
        return [
            'database',
            'mail',
        ];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        return (new MailMessage)
            ->subject(
                "Invoice issued: {$this->invoice->invoice_number}"
            )
            ->line(
                'A client invoice has been issued.'
            )
            ->line(
                "Amount: {$this->invoice->currency} {$this->invoice->total_amount}"
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'invoice-issued',

            'title' => 'Invoice issued',

            'message' => "{$this->invoice->invoice_number} was issued.",

            'url' => "/admin/invoices/{$this->invoice->public_id}",
        ];
    }
}
