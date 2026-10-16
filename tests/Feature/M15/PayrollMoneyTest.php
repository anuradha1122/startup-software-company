<?php

use App\Services\Payroll\PayrollMoneyService;

it('converts payroll amounts without binary floating point arithmetic', function (): void {
    $money =
        app(
            PayrollMoneyService::class
        );

    expect(
        $money->toMinor(
            '1000.10'
        )
    )->toBe(100010);

    expect(
        $money->toMinor(
            '0.05'
        )
    )->toBe(5);

    expect(
        $money->toMinor(
            '100000'
        )
    )->toBe(10000000);

    expect(
        $money->toMinor(
            '-1.25'
        )
    )->toBe(-125);

    expect(
        $money->fromMinor(
            100010
        )
    )->toBe('1000.10');

    expect(
        $money->fromMinor(
            5
        )
    )->toBe('0.05');

    expect(
        $money->fromMinor(
            -125
        )
    )->toBe('-1.25');
});

it('rejects invalid payroll money values', function (): void {
    $money =
        app(
            PayrollMoneyService::class
        );

    expect(
        fn () => $money->toMinor(
            '100.999'
        )
    )->toThrow(
        InvalidArgumentException::class
    );

    expect(
        fn () => $money->toMinor(
            'abc'
        )
    )->toThrow(
        InvalidArgumentException::class
    );
});
