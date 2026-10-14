<?php

namespace App\Services\Timesheets;

use App\Models\Timesheet;
use Illuminate\Support\Facades\DB;

class TimesheetCostingService
{
    public function cost(
        Timesheet $timesheet,
        ResourceCostRateService $rates
    ): void {
        $timesheet->load([
            'user',
            'entries',
        ]);

        DB::transaction(
            function () use (
                $timesheet,
                $rates
            ): void {
                foreach (
                    $timesheet->entries as $entry
                ) {
                    $rate =
                        $rates->rateFor(
                            $timesheet->user,
                            $entry->work_date
                        );

                    $amount =
                        round(
                            (float) $entry->hours
                            * (float) $rate
                                ->hourly_cost_rate,
                            2
                        );

                    $entry->update([
                        'hourly_cost_rate_snapshot' => $rate
                            ->hourly_cost_rate,

                        'internal_cost_amount' => $amount,

                        'cost_currency' => $rate->currency,

                        'costed_at' => now(),
                    ]);
                }
            }
        );
    }
}
