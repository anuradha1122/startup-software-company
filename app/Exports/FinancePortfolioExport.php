<?php

namespace App\Exports;

use App\Services\Finance\FinancePortfolioService;
use Maatwebsite\Excel\Concerns\FromArray;

class FinancePortfolioExport implements FromArray
{
    public function array(): array
    {
        $projects =
            app(
                FinancePortfolioService::class
            )->projects();

        $rows = [[
            'Project',
            'Client',
            'Currency',
            'Contracted Revenue',
            'Invoiced Revenue',
            'Collected Cash',
            'Labor Cost',
            'Non-Labor Cost',
            'Actual Cost',
            'Gross Profit',
            'Gross Margin %',
            'Outstanding Receivable',
            'Currency Mismatches',
        ]];

        foreach (
            $projects as $row
        ) {
            $project =
                $row['project'];

            $financials =
                $row['financials'];

            $rows[] = [
                $project
                    ->project_number,

                $project
                    ->client
                    ->name,

                $financials[
                    'currency'
                ],

                $financials[
                    'contracted_revenue'
                ],

                $financials[
                    'invoiced_revenue'
                ],

                $financials[
                    'collected_cash'
                ],

                $financials[
                    'labor_cost'
                ],

                $financials[
                    'non_labor_cost'
                ],

                $financials[
                    'actual_cost'
                ],

                $financials[
                    'gross_profit'
                ],

                $financials[
                    'gross_margin_percent'
                ],

                $financials[
                    'outstanding_receivable'
                ],

                $financials[
                    'currency_mismatch_count'
                ],
            ];
        }

        return $rows;
    }
}
