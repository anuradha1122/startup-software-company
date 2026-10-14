<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\ProjectExpense;
use RuntimeException;

class BillableExpenseImportService
{
    public function __construct(
        private readonly InvoiceTotalsService $totals
    ) {}

    public function import(
        Invoice $invoice,
        ProjectExpense $expense
    ): void {
        if (! $invoice->isEditable()) {
            throw new RuntimeException(
                'Only draft invoices may import expenses.'
            );
        }

        if (! $invoice->project_id) {
            throw new RuntimeException(
                'Expense imports require a project invoice.'
            );
        }

        if (
            $expense->project_id
            !== $invoice->project_id
        ) {
            throw new RuntimeException(
                'The expense does not belong to this invoice project.'
            );
        }

        if ($expense->status !== 'approved') {
            throw new RuntimeException(
                'Only approved expenses may be invoiced.'
            );
        }

        if (! $expense->billable_to_client) {
            throw new RuntimeException(
                'The expense is not marked billable to client.'
            );
        }

        if (
            strtoupper($expense->currency)
            !== strtoupper($invoice->currency)
        ) {
            throw new RuntimeException(
                'Expense and invoice currencies must match.'
            );
        }

        $exists =
            $invoice
                ->lines()
                ->where(
                    'source_type',
                    'project_expense'
                )
                ->where(
                    'source_id',
                    $expense->id
                )
                ->exists();

        if ($exists) {
            throw new RuntimeException(
                'This expense is already on the invoice.'
            );
        }

        $alreadyInvoiced =
            InvoiceLine::query()
                ->where(
                    'source_type',
                    'project_expense'
                )
                ->where(
                    'source_id',
                    $expense->id
                )
                ->exists();

        if ($alreadyInvoiced) {
            throw new RuntimeException(
                'This expense has already been invoiced.'
            );
        }

        $sortOrder =
            (int) $invoice
                ->lines()
                ->max('sort_order')
            + 1;

        $invoice
            ->lines()
            ->create([
                'sort_order' => $sortOrder,

                'description' => $expense->title,

                'quantity' => 1,

                'unit_price' => $expense->total_amount,

                'tax_rate' => 0,

                'subtotal' => $expense->total_amount,

                'tax_amount' => 0,

                'total_amount' => $expense->total_amount,

                'source_type' => 'project_expense',

                'source_id' => $expense->id,
            ]);

        $this->totals
            ->recalculate(
                $invoice
            );
    }
}
