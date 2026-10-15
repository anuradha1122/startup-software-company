<?php

namespace App\Services\Finance;

use App\Models\Project;
use Illuminate\Support\Collection;

class FinancePortfolioService
{
    public function __construct(
        private readonly ProjectProfitabilityService $profitability
    ) {}

    public function projects(): Collection
    {
        return Project::query()
            ->whereIn(
                'status',
                [
                    'active',
                    'on_hold',
                    'completed',
                ]
            )
            ->with([
                'client',
                'financeSetting',
            ])
            ->orderBy(
                'project_number'
            )
            ->get()
            ->map(
                function (
                    Project $project
                ): array {
                    $result =
                        $this->profitability
                            ->calculate(
                                $project
                            );

                    return [
                        'project' => $project,

                        'financials' => $result->toArray(),
                    ];
                }
            );
    }

    public function byCurrency(
        Collection $projects
    ): Collection {
        return $projects
            ->groupBy(
                fn (array $row) => $row[
                        'financials'
                    ][
                        'currency'
                    ]
            )
            ->map(
                function (
                    Collection $rows,
                    string $currency
                ): array {
                    return [
                        'currency' => $currency,

                        'project_count' => $rows->count(),

                        'contracted_revenue' => round(
                            (float) $rows
                                ->sum(
                                    fn (array $row) => $row[
                                            'financials'
                                        ][
                                            'contracted_revenue'
                                        ]
                                ),
                            2
                        ),

                        'invoiced_revenue' => round(
                            (float) $rows
                                ->sum(
                                    fn (array $row) => $row[
                                            'financials'
                                        ][
                                            'invoiced_revenue'
                                        ]
                                ),
                            2
                        ),

                        'collected_cash' => round(
                            (float) $rows
                                ->sum(
                                    fn (array $row) => $row[
                                            'financials'
                                        ][
                                            'collected_cash'
                                        ]
                                ),
                            2
                        ),

                        'actual_cost' => round(
                            (float) $rows
                                ->sum(
                                    fn (array $row) => $row[
                                            'financials'
                                        ][
                                            'actual_cost'
                                        ]
                                ),
                            2
                        ),

                        'gross_profit' => round(
                            (float) $rows
                                ->sum(
                                    fn (array $row) => $row[
                                            'financials'
                                        ][
                                            'gross_profit'
                                        ]
                                ),
                            2
                        ),

                        'outstanding_receivable' => round(
                            (float) $rows
                                ->sum(
                                    fn (array $row) => $row[
                                            'financials'
                                        ][
                                            'outstanding_receivable'
                                        ]
                                ),
                            2
                        ),
                    ];
                }
            )
            ->values();
    }
}
