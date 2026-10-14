<?php

namespace App\Services\Billing;

use App\Models\Invoice;

class InvoiceTotalsService
{
    public function recalculate(
        Invoice $invoice
    ): Invoice {
        $subtotal = round(
            (float) $invoice
                ->lines()
                ->sum('subtotal'),
            2
        );

        $tax = round(
            (float) $invoice
                ->lines()
                ->sum('tax_amount'),
            2
        );

        $total = round(
            $subtotal + $tax,
            2
        );

        $paid = round(
            (float) $invoice->amount_paid,
            2
        );

        $invoice->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'balance_due' => max(
                0,
                round(
                    $total - $paid,
                    2
                )
            ),
        ]);

        return $invoice->refresh();
    }
}
