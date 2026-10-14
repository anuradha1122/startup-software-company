<?php

namespace App\Mail;

use App\Models\CompanySetting;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceIssuedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Invoice $invoice
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invoice {$this->invoice->invoice_number}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.invoice-issued'
        );
    }

    public function attachments(): array
    {
        $this->invoice->load([
            'client',
            'project',
            'lines',
        ]);

        $pdf =
            Pdf::loadView(
                'pdf.invoice',
                [
                    'invoice' => $this->invoice,

                    'company' => CompanySetting::first(),
                ]
            )->output();

        return [
            Attachment::fromData(
                fn () => $pdf,
                "{$this->invoice->invoice_number}.pdf"
            )->withMime(
                'application/pdf'
            ),
        ];
    }
}
