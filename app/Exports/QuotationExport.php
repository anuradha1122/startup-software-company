<?php

namespace App\Exports;

use App\Models\Quotation;
use Maatwebsite\Excel\Concerns\FromArray;

class QuotationExport implements FromArray
{
    public function __construct(
        private readonly Quotation $quotation
    ) {}

    public function array(): array
    {
        $this->quotation->load([
            'client',
            'estimate.lines',
        ]);

        $rows = [
            [
                'Quotation',
                $this->quotation
                    ->quotation_number,
            ],
            [
                'Client',
                $this->quotation
                    ->client
                    ->name,
            ],
            [
                'Currency',
                $this->quotation
                    ->currency,
            ],
            [],
            [
                'Module',
                'Description',
                'Hours',
                'Amount',
            ],
        ];

        foreach (
            $this->quotation
                ->estimate
                ->lines as $line
        ) {
            $rows[] = [
                $line->module_name,
                $line->description,
                $line->estimated_hours,
                $line->revenue_amount,
            ];
        }

        $rows[] = [];
        $rows[] = [
            'Subtotal',
            $this->quotation->subtotal,
        ];

        $rows[] = [
            'Discount',
            $this->quotation
                ->discount_amount,
        ];

        $rows[] = [
            'Tax',
            $this->quotation
                ->tax_amount,
        ];

        $rows[] = [
            'Total',
            $this->quotation
                ->total_amount,
        ];

        return $rows;
    }
}
