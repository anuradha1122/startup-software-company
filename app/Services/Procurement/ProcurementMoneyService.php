<?php

namespace App\Services\Procurement;

use InvalidArgumentException;

class ProcurementMoneyService
{
    public function toMinor(
        string|int $value
    ): int {
        $value = trim(
            (string) $value
        );

        if (! preg_match(
            '/^-?\d+(?:\.\d{1,2})?$/',
            $value
        )) {
            throw new InvalidArgumentException(
                'Invalid monetary value.'
            );
        }

        $negative =
            str_starts_with(
                $value,
                '-'
            );

        $value =
            ltrim(
                $value,
                '-'
            );

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

        $fraction =
            str_pad(
                $fraction,
                2,
                '0'
            );

        $minor =
            ((int) $whole * 100)
            + (int) substr(
                $fraction,
                0,
                2
            );

        return $negative
            ? -$minor
            : $minor;
    }

    public function fromMinor(
        int $minor
    ): string {
        $negative = $minor < 0;

        $minor = abs($minor);

        $value =
            intdiv(
                $minor,
                100
            )
            .'.'
            .str_pad(
                (string) ($minor % 100),
                2,
                '0',
                STR_PAD_LEFT
            );

        return $negative
            ? '-'.$value
            : $value;
    }
}
