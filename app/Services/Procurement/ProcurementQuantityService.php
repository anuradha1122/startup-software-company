<?php

namespace App\Services\Procurement;

use InvalidArgumentException;

class ProcurementQuantityService
{
    public function toHundredths(
        string|int $value
    ): int {
        $value = trim(
            (string) $value
        );

        if (! preg_match(
            '/^\d+(?:\.\d{1,2})?$/',
            $value
        )) {
            throw new InvalidArgumentException(
                'Invalid quantity.'
            );
        }

        [$whole, $fraction] =
            array_pad(
                explode(
                    '.',
                    $value,
                    2
                ),
                2,
                ''
            );

        return ((int) $whole * 100)
            + (int) str_pad(
                $fraction,
                2,
                '0'
            );
    }

    public function fromHundredths(
        int $value
    ): string {
        return intdiv(
            $value,
            100
        )
        .'.'
        .str_pad(
            (string) ($value % 100),
            2,
            '0',
            STR_PAD_LEFT
        );
    }
}
