<?php

namespace App\Services\Estimation;

use App\Models\Estimate;

class EstimateCalculatorService
{
    public function recalculate(
        Estimate $estimate
    ): void {
        $estimate->load('lines');

        $hours = 0.0;
        $baseRevenue = 0.0;
        $cost = 0.0;

        foreach ($estimate->lines as $line) {
            $lineRevenue =
                (float) $line->estimated_hours
                * (float) $line->billing_rate;

            $lineCost =
                (float) $line->estimated_hours
                * (float) $line->cost_rate;

            $line->updateQuietly([
                'revenue_amount' => round(
                    $lineRevenue,
                    2
                ),

                'cost_amount' => round(
                    $lineCost,
                    2
                ),
            ]);

            $hours +=
                (float) $line->estimated_hours;

            $baseRevenue +=
                $lineRevenue;

            $cost +=
                $lineCost;
        }

        $contingency =
            $baseRevenue
            * (
                (float)
                $estimate->contingency_percent
                / 100
            );

        $revenue =
            $baseRevenue
            + $contingency;

        $margin =
            $revenue
            - $cost;

        $marginPercent =
            $revenue > 0
                ? ($margin / $revenue) * 100
                : 0;

        $estimate->updateQuietly([
            'total_hours' => round($hours, 2),

            'estimated_revenue' => round($revenue, 2),

            'estimated_cost' => round($cost, 2),

            'gross_margin' => round($margin, 2),

            'gross_margin_percent' => round(
                $marginPercent,
                2
            ),
        ]);
    }
}
