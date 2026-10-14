<?php

namespace App\Services\Billing;

use App\Models\Invoice;

class InvoiceStatusService
{
    public function refresh(
        Invoice $invoice
    ): Invoice {
        if (
            in_array(
                $invoice->status,
                ['draft', 'void'],
                true
            )
        ) {
            return $invoice;
        }

        $balance =
            round(
                (float) $invoice->balance_due,
                2
            );

        $paid =
            round(
                (float) $invoice->amount_paid,
                2
            );

        if ($balance <= 0) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => $invoice->paid_at
                    ?? now(),
            ]);

            return $invoice->refresh();
        }

        if (
            $invoice->due_date->isPast()
        ) {
            $invoice->update([
                'status' => 'overdue',
                'paid_at' => null,
            ]);

            return $invoice->refresh();
        }

        if ($paid > 0) {
            $invoice->update([
                'status' => 'partially_paid',

                'paid_at' => null,
            ]);

            return $invoice->refresh();
        }

        $invoice->update([
            'status' => 'issued',
            'paid_at' => null,
        ]);

        return $invoice->refresh();
    }
}
