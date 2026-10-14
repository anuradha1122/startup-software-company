<?php

namespace App\Services\Expenses;

class ExpenseCalculatorService
{
    public function calculate(
        float $subtotal,
        float $taxAmount
    ): array {
        $subtotal =
            round(
                max(0, $subtotal),
                2
            );

        $taxAmount =
            round(
                max(0, $taxAmount),
                2
            );

        return [
            'subtotal' => $subtotal,

            'tax_amount' => $taxAmount,

            'total_amount' => round(
                $subtotal
                + $taxAmount,
                2
            ),
        ];
    }
}
