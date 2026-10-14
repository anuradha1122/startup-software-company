<?php

use App\Services\Expenses\ExpenseCalculatorService;

it('calculates expense total', function (): void {
    $result =
        app(
            ExpenseCalculatorService::class
        )->calculate(
            10000,
            1800
        );

    expect(
        $result['subtotal']
    )->toBe(10000.0);

    expect(
        $result['tax_amount']
    )->toBe(1800.0);

    expect(
        $result['total_amount']
    )->toBe(11800.0);
});
