<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\ProjectExpense;
use Maatwebsite\Excel\Concerns\FromArray;

class ProjectNonLaborCostExport implements FromArray
{
    public function __construct(
        private readonly Project $project
    ) {}

    public function array(): array
    {
        $expenses =
            ProjectExpense::query()
                ->where(
                    'project_id',
                    $this->project->id
                )
                ->where(
                    'status',
                    'approved'
                )
                ->with([
                    'supplier',
                    'incurredBy',
                ])
                ->orderBy(
                    'expense_date'
                )
                ->get();

        $rows = [
            [
                'Expense',
                'Date',
                'Type',
                'Supplier',
                'Title',
                'Currency',
                'Total',
            ],
        ];

        foreach (
            $expenses as $expense
        ) {
            $rows[] = [
                $expense
                    ->expense_number,

                $expense
                    ->expense_date
                    ->format('Y-m-d'),

                $expense->type,

                $expense
                    ->supplier
                    ?->name,

                $expense->title,

                $expense->currency,

                $expense->total_amount,
            ];
        }

        $rows[] = [];

        $rows[] = [
            'Total Non-Labor Cost',
            $expenses->sum(
                'total_amount'
            ),
        ];

        return $rows;
    }
}
