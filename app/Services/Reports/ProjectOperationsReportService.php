<?php

namespace App\Services\Reports;

use App\Models\Project;
use App\Services\Finance\ProjectProfitabilityService;
use Illuminate\Support\Collection;

class ProjectOperationsReportService
{
    public function __construct(
        private readonly ProjectProfitabilityService $profitability
    ) {}

    public function rows(): Collection
    {
        return Project::query()
            ->with([
                'client',
            ])
            ->whereIn(
                'status',
                [
                    'planned',
                    'active',
                    'on_hold',
                    'completed',
                ]
            )
            ->orderBy(
                'project_number'
            )
            ->get()
            ->map(
                function (
                    Project $project
                ): array {
                    $financials =
                        $this->profitability
                            ->calculate(
                                $project
                            );

                    $activeTeamCount =
                        $project
                            ->assignments()
                            ->where(
                                'status',
                                'active'
                            )
                            ->count();

                    $openTaskCount =
                        $project
                            ->tasks()
                            ->whereHas(
                                'workflowStatus',
                                fn ($query) => $query->where(
                                    'slug',
                                    '!=',
                                    'done'
                                )
                            )
                            ->count();

                    $pendingApprovalCount =
                        $project
                            ->clientApprovalRequests()
                            ->where(
                                'status',
                                'pending'
                            )
                            ->count();

                    return [
                        'project_public_id' => $project->public_id,

                        'project_number' => $project->project_number,

                        'project_name' => $project->name,

                        'project_status' => $project->status,

                        'client_name' => $project->client->name,

                        'currency' => $financials->currency,

                        'active_team_count' => $activeTeamCount,

                        'open_task_count' => $openTaskCount,

                        'pending_client_approval_count' => $pendingApprovalCount,

                        'contracted_revenue' => $financials
                            ->contractedRevenue,

                        'invoiced_revenue' => $financials
                            ->invoicedRevenue,

                        'collected_cash' => $financials
                            ->collectedCash,

                        'actual_cost' => $financials
                            ->actualCost,

                        'gross_profit' => $financials
                            ->grossProfit,

                        'gross_margin_percent' => $financials
                            ->grossMarginPercent,

                        'outstanding_receivable' => $financials
                            ->outstandingReceivable,

                        'currency_mismatch_count' => $financials
                            ->currencyMismatchCount(),
                    ];
                }
            );
    }
}
