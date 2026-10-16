<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;

class ProjectOperationsExport implements FromArray
{
    public function __construct(
        private readonly Collection $rows
    ) {}

    public function array(): array
    {
        $output = [[
            'Project',
            'Name',
            'Client',
            'Status',
            'Currency',
            'Active Team',
            'Open Tasks',
            'Pending Client Approvals',
            'Contracted Revenue',
            'Invoiced Revenue',
            'Collected Cash',
            'Actual Cost',
            'Gross Profit',
            'Gross Margin %',
            'Outstanding Receivable',
            'Currency Mismatches',
        ]];

        foreach (
            $this->rows as $row
        ) {
            $output[] = [
                $row[
                    'project_number'
                ],

                $row[
                    'project_name'
                ],

                $row[
                    'client_name'
                ],

                $row[
                    'project_status'
                ],

                $row[
                    'currency'
                ],

                $row[
                    'active_team_count'
                ],

                $row[
                    'open_task_count'
                ],

                $row[
                    'pending_client_approval_count'
                ],

                $row[
                    'contracted_revenue'
                ],

                $row[
                    'invoiced_revenue'
                ],

                $row[
                    'collected_cash'
                ],

                $row[
                    'actual_cost'
                ],

                $row[
                    'gross_profit'
                ],

                $row[
                    'gross_margin_percent'
                ],

                $row[
                    'outstanding_receivable'
                ],

                $row[
                    'currency_mismatch_count'
                ],
            ];
        }

        return $output;
    }
}
