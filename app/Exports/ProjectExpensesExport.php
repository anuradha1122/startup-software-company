<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;

class ProjectExpensesExport implements FromArray
{
    public function __construct(
        private readonly Project $project
    ) {}

    public function array(): array
    {
        $this->project->load([
            'expenses.supplier',
            'expenses.incurredBy',
        ]);

        $rows = [
            [
                'Expense',
                'Date',
                'Type',
                'Title',
                'Supplier',
                'Incurred By',
                'Currency',
                'Subtotal',
                'Tax',
                'Total',
                'Billable',
                'Status',
            ],
        ];

        foreach (
            $this->project
                ->expenses
                ->sortBy(
                    'expense_date'
                ) as $expense
        ) {
            $rows[] = [
                $expense
                    ->expense_number,

                $expense
                    ->expense_date
                    ->format('Y-m-d'),

                $expense->type,

                $expense->title,

                $expense
                    ->supplier
                    ?->name,

                $expense
                    ->incurredBy
                    ?->name,

                $expense->currency,

                $expense->subtotal,

                $expense->tax_amount,

                $expense->total_amount,

                $expense
                    ->billable_to_client
                    ? 'Yes'
                    : 'No',

                $expense->status,
            ];
        }

        return $rows;
    }
}
