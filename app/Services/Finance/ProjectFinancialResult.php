<?php

namespace App\Services\Finance;

final readonly class ProjectFinancialResult
{
    public function __construct(
        public string $currency,
        public float $contractedRevenue,
        public float $invoicedRevenue,
        public float $collectedCash,
        public float $laborCost,
        public float $nonLaborCost,
        public float $actualCost,
        public float $grossProfit,
        public ?float $grossMarginPercent,
        public float $unbilledRevenue,
        public float $outstandingReceivable,
        public ?float $collectionPercent,
        public ?float $costConsumptionPercent,
        public ?float $invoiceProgressPercent,
        public array $currencyMismatches,
    ) {}

    public function currencyMismatchCount(): int
    {
        return count(
            $this->currencyMismatches
        );
    }

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,

            'contracted_revenue' => $this->contractedRevenue,

            'invoiced_revenue' => $this->invoicedRevenue,

            'collected_cash' => $this->collectedCash,

            'labor_cost' => $this->laborCost,

            'non_labor_cost' => $this->nonLaborCost,

            'actual_cost' => $this->actualCost,

            'gross_profit' => $this->grossProfit,

            'gross_margin_percent' => $this->grossMarginPercent,

            'unbilled_revenue' => $this->unbilledRevenue,

            'outstanding_receivable' => $this->outstandingReceivable,

            'collection_percent' => $this->collectionPercent,

            'cost_consumption_percent' => $this->costConsumptionPercent,

            'invoice_progress_percent' => $this->invoiceProgressPercent,

            'currency_mismatches' => $this->currencyMismatches,

            'currency_mismatch_count' => $this->currencyMismatchCount(),
        ];
    }
}
