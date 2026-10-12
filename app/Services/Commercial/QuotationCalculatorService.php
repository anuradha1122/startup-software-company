<?php

namespace App\Services\Commercial;

use App\Models\Quotation;

class QuotationCalculatorService
{
    public function calculate(
        Quotation $quotation
    ): void {
        $subtotal =
            (float) $quotation
                ->estimate
                ->estimated_revenue;

        $discountPercent =
            (float) $quotation
                ->discount_percent;

        $taxPercent =
            (float) $quotation
                ->tax_percent;

        $discountAmount =
            $subtotal
            * ($discountPercent / 100);

        $taxable =
            $subtotal
            - $discountAmount;

        $taxAmount =
            $taxable
            * ($taxPercent / 100);

        $total =
            $taxable
            + $taxAmount;

        $quotation->updateQuietly([
            'subtotal' => round($subtotal, 2),

            'discount_amount' => round(
                $discountAmount,
                2
            ),

            'tax_amount' => round(
                $taxAmount,
                2
            ),

            'total_amount' => round($total, 2),
        ]);
    }
}
