<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;

class InvoicesExport implements FromArray
{
    public function array(): array
    {
        $rows = [[
            'Invoice',
            'Client',
            'Project',
            'Invoice Date',
            'Due Date',
            'Status',
            'Currency',
            'Subtotal',
            'Tax',
            'Total',
            'Paid',
            'Balance',
        ]];

        $invoices =
            Invoice::query()
                ->with([
                    'client',
                    'project',
                ])
                ->latest(
                    'invoice_date'
                )
                ->get();

        foreach ($invoices as $invoice) {
            $rows[] = [
                $invoice->invoice_number,
                $invoice->client->name,
                $invoice->project
                    ?->project_number,
                $invoice->invoice_date
                    ->format('Y-m-d'),
                $invoice->due_date
                    ->format('Y-m-d'),
                $invoice->status,
                $invoice->currency,
                $invoice->subtotal,
                $invoice->tax_amount,
                $invoice->total_amount,
                $invoice->amount_paid,
                $invoice->balance_due,
            ];
        }

        return $rows;
    }
}
