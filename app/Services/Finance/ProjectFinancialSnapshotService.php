<?php

namespace App\Services\Finance;

use App\Models\Project;
use App\Models\ProjectFinancialSnapshot;
use App\Models\User;
use RuntimeException;

class ProjectFinancialSnapshotService
{
    public function __construct(
        private readonly ProjectProfitabilityService $profitability
    ) {}

    public function capture(
        Project $project,
        ?User $generatedBy = null
    ): ProjectFinancialSnapshot {
        $result =
            $this->profitability
                ->calculate(
                    $project
                );

        if (
            $result
                ->currencyMismatchCount()
            > 0
        ) {
            throw new RuntimeException(
                'Financial snapshot cannot be created while source currency mismatches exist.'
            );
        }

        $snapshotDate =
            today()->toDateString();

        $snapshot =
            ProjectFinancialSnapshot::query()
                ->where(
                    'project_id',
                    $project->id
                )
                ->whereDate(
                    'snapshot_date',
                    $snapshotDate
                )
                ->first();

        $data = [
            'currency' => $result->currency,

            'contracted_revenue' => $result->contractedRevenue,

            'invoiced_revenue' => $result->invoicedRevenue,

            'collected_cash' => $result->collectedCash,

            'labor_cost' => $result->laborCost,

            'non_labor_cost' => $result->nonLaborCost,

            'actual_cost' => $result->actualCost,

            'gross_profit' => $result->grossProfit,

            'gross_margin_percent' => $result->grossMarginPercent,

            'unbilled_revenue' => $result->unbilledRevenue,

            'outstanding_receivable' => $result
                ->outstandingReceivable,

            'collection_percent' => $result->collectionPercent,

            'cost_consumption_percent' => $result
                ->costConsumptionPercent,

            'invoice_progress_percent' => $result
                ->invoiceProgressPercent,

            'currency_mismatch_count' => 0,

            'generated_by' => $generatedBy?->id,
        ];

        if ($snapshot) {
            $snapshot->update(
                $data
            );

            return $snapshot->refresh();
        }

        return ProjectFinancialSnapshot::create([
            'project_id' => $project->id,

            'snapshot_date' => $snapshotDate,

            ...$data,
        ]);
    }
}
