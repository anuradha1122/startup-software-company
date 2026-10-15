<?php

namespace App\Services\Finance;

use App\Models\Invoice;
use App\Models\PaymentAllocation;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\TimesheetEntry;

class ProjectProfitabilityService
{
    public function calculate(
        Project $project
    ): ProjectFinancialResult {
        $currency =
            strtoupper(
                (string) $project
                    ->getAttribute(
                        'currency'
                    )
            );

        $contractedRevenue =
            round(
                (float) (
                    $project
                        ->getAttribute(
                            'contract_value'
                        )
                    ?? 0
                ),
                2
            );

        $currencyMismatches = [];

        $laborQuery =
            TimesheetEntry::query()
                ->where(
                    'project_id',
                    $project->id
                )
                ->whereHas(
                    'timesheet',
                    fn ($query) => $query->where(
                        'status',
                        'approved'
                    )
                );

        $laborMismatchCount =
            (clone $laborQuery)
                ->whereNotNull(
                    'cost_currency'
                )
                ->where(
                    'cost_currency',
                    '!=',
                    $currency
                )
                ->count();

        if ($laborMismatchCount > 0) {
            $currencyMismatches[] = [
                'source' => 'timesheet_entries',

                'count' => $laborMismatchCount,

                'message' => 'Approved labor costs contain a currency different from the project currency.',
            ];
        }

        $laborCost =
            round(
                (float) (clone $laborQuery)
                    ->where(
                        'cost_currency',
                        $currency
                    )
                    ->sum(
                        'internal_cost_amount'
                    ),
                2
            );

        $expenseQuery =
            ProjectExpense::query()
                ->where(
                    'project_id',
                    $project->id
                )
                ->where(
                    'status',
                    'approved'
                );

        $expenseMismatchCount =
            (clone $expenseQuery)
                ->where(
                    'currency',
                    '!=',
                    $currency
                )
                ->count();

        if ($expenseMismatchCount > 0) {
            $currencyMismatches[] = [
                'source' => 'project_expenses',

                'count' => $expenseMismatchCount,

                'message' => 'Approved non-labor costs contain a currency different from the project currency.',
            ];
        }

        $nonLaborCost =
            round(
                (float) (clone $expenseQuery)
                    ->where(
                        'currency',
                        $currency
                    )
                    ->sum(
                        'total_amount'
                    ),
                2
            );

        $invoiceQuery =
            Invoice::query()
                ->where(
                    'project_id',
                    $project->id
                )
                ->whereIn(
                    'status',
                    [
                        'issued',
                        'partially_paid',
                        'paid',
                        'overdue',
                    ]
                );

        $invoiceMismatchCount =
            (clone $invoiceQuery)
                ->where(
                    'currency',
                    '!=',
                    $currency
                )
                ->count();

        if ($invoiceMismatchCount > 0) {
            $currencyMismatches[] = [
                'source' => 'invoices',

                'count' => $invoiceMismatchCount,

                'message' => 'Issued invoices contain a currency different from the project currency.',
            ];
        }

        $matchingInvoices =
            (clone $invoiceQuery)
                ->where(
                    'currency',
                    $currency
                );

        $invoicedRevenue =
            round(
                (float) (
                    clone $matchingInvoices
                )->sum(
                    'total_amount'
                ),
                2
            );

        $outstandingReceivable =
            round(
                (float) (
                    clone $matchingInvoices
                )
                    ->where(
                        'balance_due',
                        '>',
                        0
                    )
                    ->sum(
                        'balance_due'
                    ),
                2
            );

        $matchingInvoiceIds =
            (clone $matchingInvoices)
                ->pluck('id');

        $collectedCash =
            round(
                (float) PaymentAllocation::query()
                    ->whereIn(
                        'invoice_id',
                        $matchingInvoiceIds
                    )
                    ->sum('amount'),
                2
            );

        $actualCost =
            round(
                $laborCost
                + $nonLaborCost,
                2
            );

        $grossProfit =
            round(
                $contractedRevenue
                - $actualCost,
                2
            );

        $grossMarginPercent =
            $contractedRevenue > 0
                ? round(
                    (
                        $grossProfit
                        / $contractedRevenue
                    ) * 100,
                    4
                )
                : null;

        $unbilledRevenue =
            round(
                max(
                    0,
                    $contractedRevenue
                    - $invoicedRevenue
                ),
                2
            );

        $collectionPercent =
            $invoicedRevenue > 0
                ? round(
                    (
                        $collectedCash
                        / $invoicedRevenue
                    ) * 100,
                    4
                )
                : null;

        $costConsumptionPercent =
            $contractedRevenue > 0
                ? round(
                    (
                        $actualCost
                        / $contractedRevenue
                    ) * 100,
                    4
                )
                : null;

        $invoiceProgressPercent =
            $contractedRevenue > 0
                ? round(
                    (
                        $invoicedRevenue
                        / $contractedRevenue
                    ) * 100,
                    4
                )
                : null;

        return new ProjectFinancialResult(
            currency: $currency,

            contractedRevenue: $contractedRevenue,

            invoicedRevenue: $invoicedRevenue,

            collectedCash: $collectedCash,

            laborCost: $laborCost,

            nonLaborCost: $nonLaborCost,

            actualCost: $actualCost,

            grossProfit: $grossProfit,

            grossMarginPercent: $grossMarginPercent,

            unbilledRevenue: $unbilledRevenue,

            outstandingReceivable: $outstandingReceivable,

            collectionPercent: $collectionPercent,

            costConsumptionPercent: $costConsumptionPercent,

            invoiceProgressPercent: $invoiceProgressPercent,

            currencyMismatches: $currencyMismatches,
        );
    }
}
