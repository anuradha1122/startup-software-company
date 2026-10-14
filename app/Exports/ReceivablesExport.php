<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;

class ReceivablesExport implements FromArray
{
    public function array(): array
    {
        $rows = [[
            'Invoice',
            'Client',
            'Due Date',
            'Status',
            'Currency',
            'Total',
            'Paid',
            'Outstanding',
        ]];

        $invoices =
            Invoice::query()
                ->with('client')
                ->whereIn(
                    'status',
                    [
                        'issued',
                        'partially_paid',
                        'overdue',
                    ]
                )
                ->where(
                    'balance_due',
                    '>',
                    0
                )
                ->orderBy(
                    'due_date'
                )
                ->get();

        foreach ($invoices as $invoice) {
            $rows[] = [
                $invoice->invoice_number,
                $invoice->client->name,
                $invoice->due_date
                    ->format('Y-m-d'),
                $invoice->status,
                $invoice->currency,
                $invoice->total_amount,
                $invoice->amount_paid,
                $invoice->balance_due,
            ];
        }

        return $rows;
    }
}
