<?php

namespace App\Services\Billing;

use App\Mail\InvoiceIssuedMail;
use App\Models\Invoice;
use App\Models\User;
use App\Notifications\InvoiceIssuedInternalNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

class InvoiceIssueService
{
    public function __construct(
        private readonly InvoiceTotalsService $totals
    ) {}

    public function issue(
        Invoice $invoice,
        User $issuer
    ): Invoice {
        if (! $invoice->isEditable()) {
            throw new RuntimeException(
                'Only draft invoices may be issued.'
            );
        }

        if (
            $invoice->lines()
                ->doesntExist()
        ) {
            throw new RuntimeException(
                'An invoice must contain at least one line before issue.'
            );
        }

        $invoice =
            $this->totals
                ->recalculate(
                    $invoice
                );

        if (
            (float) $invoice->total_amount
            <= 0
        ) {
            throw new RuntimeException(
                'Invoice total must be greater than zero.'
            );
        }

        $invoice->update([
            'status' => 'issued',

            'amount_paid' => 0,

            'balance_due' => $invoice->total_amount,

            'issued_at' => now(),

            'issued_by' => $issuer->id,
        ]);

        $invoice->refresh();

        if ($invoice->billing_email) {
            Mail::to(
                $invoice->billing_email
            )->queue(
                new InvoiceIssuedMail(
                    $invoice
                )
            );

            $invoice->update([
                'last_sent_at' => now(),
            ]);
        }

        $internal =
            User::query()
                ->permission(
                    'invoices.view'
                )
                ->where(
                    'status',
                    'active'
                )
                ->where(
                    'id',
                    '!=',
                    $issuer->id
                )
                ->get();

        Notification::send(
            $internal,
            new InvoiceIssuedInternalNotification(
                $invoice
            )
        );

        return $invoice->refresh();
    }

    public function send(
        Invoice $invoice
    ): Invoice {
        if (
            $invoice->status === 'draft'
            || $invoice->status === 'void'
        ) {
            throw new RuntimeException(
                'Only issued invoices may be sent.'
            );
        }

        if (! $invoice->billing_email) {
            throw new RuntimeException(
                'The invoice does not have a billing email address.'
            );
        }

        Mail::to(
            $invoice->billing_email
        )->queue(
            new InvoiceIssuedMail(
                $invoice
            )
        );

        $invoice->update([
            'last_sent_at' => now(),
        ]);

        return $invoice->refresh();
    }
}
