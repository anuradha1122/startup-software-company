<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\Finance\ProjectFinancialSnapshotService;
use App\Services\Finance\ProjectMarginAlertService;
use Illuminate\Console\Command;
use Throwable;

class CaptureProjectFinancialSnapshots extends Command
{
    protected $signature =
        'finance:capture-snapshots';

    protected $description =
        'Capture project financial snapshots and evaluate project margin alerts';

    public function handle(
        ProjectFinancialSnapshotService $snapshots,
        ProjectMarginAlertService $alerts
    ): int {
        Project::query()
            ->whereIn(
                'status',
                [
                    'active',
                    'on_hold',
                    'completed',
                ]
            )
            ->chunkById(
                100,
                function ($projects) use (
                    $snapshots,
                    $alerts
                ): void {
                    foreach (
                        $projects as $project
                    ) {
                        try {
                            $snapshots->capture(
                                $project
                            );

                            $alerts->check(
                                $project
                            );
                        } catch (
                            Throwable $exception
                        ) {
                            report(
                                $exception
                            );

                            $this->warn(
                                "{$project->project_number}: {$exception->getMessage()}"
                            );
                        }
                    }
                }
            );

        $this->info(
            'Project finance snapshot processing completed.'
        );

        return self::SUCCESS;
    }
}
