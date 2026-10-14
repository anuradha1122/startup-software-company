<?php

use App\Services\Billing\InvoiceCalculatorService;

it('calculates invoice line subtotal tax and total', function (): void {
    $result =
        app(
            InvoiceCalculatorService::class
        )->line(
            2,
            10000,
            18
        );

    expect(
        $result['subtotal']
    )->toBe(20000.0);

    expect(
        $result['tax_amount']
    )->toBe(3600.0);

    expect(
        $result['total_amount']
    )->toBe(23600.0);
});
