<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\Billing\InvoiceStatusService;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    protected $signature =
        'invoices:mark-overdue';

    protected $description =
        'Mark outstanding invoices overdue when their due date has passed';

    public function handle(
        InvoiceStatusService $statuses
    ): int {
        Invoice::query()
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
            ->chunkById(
                100,
                function ($invoices) use (
                    $statuses
                ): void {
                    foreach (
                        $invoices as $invoice
                    ) {
                        $statuses->refresh(
                            $invoice
                        );
                    }
                }
            );

        $this->info(
            'Invoice statuses refreshed.'
        );

        return self::SUCCESS;
    }
}
