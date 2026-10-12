<?php

namespace App\Exports;

use App\Models\Estimate;
use Maatwebsite\Excel\Concerns\FromArray;

class EstimateExport implements FromArray
{
    public function __construct(
        private readonly Estimate $estimate
    ) {}

    public function array(): array
    {
        $this->estimate->load([
            'lines',
            'requirementVersion.requirement.client',
        ]);

        $rows = [
            [
                'Estimate',
                $this->estimate
                    ->estimate_number,
            ],

            [
                'Client',
                $this->estimate
                    ->requirementVersion
                    ->requirement
                    ->client
                    ->name,
            ],

            [
                'Requirement',
                $this->estimate
                    ->requirementVersion
                    ->requirement
                    ->requirement_number,
            ],

            [
                'Version',
                $this->estimate
                    ->requirementVersion
                    ->version_number,
            ],

            [],
            [
                'Module',
                'Description',
                'Hours',
                'Billing Rate',
                'Cost Rate',
                'Revenue',
                'Cost',
            ],
        ];

        foreach (
            $this->estimate->lines as $line
        ) {
            $rows[] = [
                $line->module_name,
                $line->description,
                $line->estimated_hours,
                $line->billing_rate,
                $line->cost_rate,
                $line->revenue_amount,
                $line->cost_amount,
            ];
        }

        $rows[] = [];
        $rows[] = [
            'Total Hours',
            $this->estimate
                ->total_hours,
        ];

        $rows[] = [
            'Estimated Revenue',
            $this->estimate
                ->estimated_revenue,
        ];

        $rows[] = [
            'Estimated Cost',
            $this->estimate
                ->estimated_cost,
        ];

        $rows[] = [
            'Gross Margin',
            $this->estimate
                ->gross_margin,
        ];

        $rows[] = [
            'Gross Margin %',
            $this->estimate
                ->gross_margin_percent,
        ];

        return $rows;
    }
}
