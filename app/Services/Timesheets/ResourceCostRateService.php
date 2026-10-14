<?php

namespace App\Services\Timesheets;

use App\Models\ResourceCostRate;
use App\Models\User;
use Carbon\CarbonInterface;
use RuntimeException;

class ResourceCostRateService
{
    public function rateFor(
        User $user,
        CarbonInterface $date
    ): ResourceCostRate {
        $rate =
            ResourceCostRate::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'active',
                    true
                )
                ->whereDate(
                    'effective_from',
                    '<=',
                    $date->toDateString()
                )
                ->where(function ($query) use (
                    $date
                ): void {
                    $query
                        ->whereNull(
                            'effective_to'
                        )
                        ->orWhereDate(
                            'effective_to',
                            '>=',
                            $date->toDateString()
                        );
                })
                ->orderByDesc(
                    'effective_from'
                )
                ->first();

        if (! $rate) {
            throw new RuntimeException(
                "No internal cost rate is configured for {$user->name} on {$date->toDateString()}."
            );
        }

        return $rate;
    }

    public function assertNoOverlap(
        int $userId,
        CarbonInterface $from,
        ?CarbonInterface $to = null
    ): void {
        $overlap =
            ResourceCostRate::query()
                ->where(
                    'user_id',
                    $userId
                )
                ->where(
                    'active',
                    true
                )
                ->whereDate(
                    'effective_from',
                    '<=',
                    (
                        $to
                        ?? now()->addYears(100)
                    )->toDateString()
                )
                ->where(function ($query) use (
                    $from
                ): void {
                    $query
                        ->whereNull(
                            'effective_to'
                        )
                        ->orWhereDate(
                            'effective_to',
                            '>=',
                            $from->toDateString()
                        );
                })
                ->exists();

        if ($overlap) {
            throw new RuntimeException(
                'The internal cost rate overlaps an existing rate period.'
            );
        }
    }
}
