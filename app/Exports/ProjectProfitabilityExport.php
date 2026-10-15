<?php

namespace App\Exports;

use App\Models\Project;
use App\Services\Finance\ProjectProfitabilityService;
use Maatwebsite\Excel\Concerns\FromArray;

class ProjectProfitabilityExport implements FromArray
{
    public function __construct(
        private readonly Project $project
    ) {}

    public function array(): array
    {
        $financials =
            app(
                ProjectProfitabilityService::class
            )->calculate(
                $this->project
            );

        return [
            [
                'Project',
                $this->project
                    ->project_number,
            ],
            [
                'Name',
                $this->project->name,
            ],
            [
                'Currency',
                $financials->currency,
            ],
            [],
            [
                'Metric',
                'Amount / Percentage',
            ],
            [
                'Contracted Revenue',
                $financials
                    ->contractedRevenue,
            ],
            [
                'Invoiced Revenue',
                $financials
                    ->invoicedRevenue,
            ],
            [
                'Collected Cash',
                $financials
                    ->collectedCash,
            ],
            [
                'Labor Cost',
                $financials
                    ->laborCost,
            ],
            [
                'Non-Labor Cost',
                $financials
                    ->nonLaborCost,
            ],
            [
                'Actual Cost',
                $financials
                    ->actualCost,
            ],
            [
                'Gross Profit',
                $financials
                    ->grossProfit,
            ],
            [
                'Gross Margin %',
                $financials
                    ->grossMarginPercent,
            ],
            [
                'Unbilled Revenue',
                $financials
                    ->unbilledRevenue,
            ],
            [
                'Outstanding Receivable',
                $financials
                    ->outstandingReceivable,
            ],
            [
                'Collection %',
                $financials
                    ->collectionPercent,
            ],
            [
                'Cost Consumption %',
                $financials
                    ->costConsumptionPercent,
            ],
            [
                'Invoice Progress %',
                $financials
                    ->invoiceProgressPercent,
            ],
            [
                'Currency Mismatches',
                $financials
                    ->currencyMismatchCount(),
            ],
        ];
    }
}
