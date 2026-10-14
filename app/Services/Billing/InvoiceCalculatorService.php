<?php

namespace App\Services\Billing;

class InvoiceCalculatorService
{
    public function line(
        float $quantity,
        float $unitPrice,
        float $taxRate
    ): array {
        $quantity = round(
            max(0, $quantity),
            2
        );

        $unitPrice = round(
            max(0, $unitPrice),
            2
        );

        $taxRate = round(
            max(0, $taxRate),
            4
        );

        $subtotal = round(
            $quantity * $unitPrice,
            2
        );

        $taxAmount = round(
            $subtotal
            * ($taxRate / 100),
            2
        );

        return [
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => round(
                $subtotal + $taxAmount,
                2
            ),
        ];
    }
}
