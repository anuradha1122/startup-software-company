<?php

namespace App\Services\Leave;

use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

class LeaveDayCalculator
{
    public function calculate(
        string $start,
        string $end
    ): string {
        $startDate =
            CarbonImmutable::parse(
                $start
            )->startOfDay();

        $endDate =
            CarbonImmutable::parse(
                $end
            )->startOfDay();

        if (
            $endDate->lt(
                $startDate
            )
        ) {
            throw ValidationException::withMessages([
                'end_date' => 'End date must be on or after the start date.',
            ]);
        }

        $days = 0;

        for (
            $date = $startDate;
            $date->lte($endDate);
            $date = $date->addDay()
        ) {
            if (! $date->isWeekend()) {
                $days++;
            }
        }

        return number_format(
            $days,
            2,
            '.',
            ''
        );
    }
}
