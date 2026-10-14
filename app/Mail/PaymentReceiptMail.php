<?php

namespace App\Mail;

use App\Models\CompanySetting;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Payment $payment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Payment receipt {$this->payment->payment_number}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.payment-receipt'
        );
    }

    public function attachments(): array
    {
        $this->payment->load([
            'client',
            'allocations.invoice',
        ]);

        $pdf =
            Pdf::loadView(
                'pdf.payment-receipt',
                [
                    'payment' => $this->payment,

                    'company' => CompanySetting::first(),
                ]
            )->output();

        return [
            Attachment::fromData(
                fn () => $pdf,
                "{$this->payment->payment_number}.pdf"
            )->withMime(
                'application/pdf'
            ),
        ];
    }
}
