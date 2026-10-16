<?php

namespace App\Console\Commands;

use App\Services\Operations\OperationalHealthAlertService;
use Illuminate\Console\Command;

class CheckOperationalHealth extends Command
{
    protected $signature =
        'operations:health-check';

    protected $description =
        'Check operational health and notify administrators when attention is required';

    public function handle(
        OperationalHealthAlertService $service
    ): int {
        $result =
            $service->run();

        $this->info(
            "Operational status: {$result['status']}"
        );

        foreach (
            $result['checks'] as $check
        ) {
            $this->line(
                sprintf(
                    '[%s] %s: %s',
                    strtoupper(
                        $check['status']
                    ),
                    $check['label'],
                    $check['message']
                )
            );
        }

        return $result['status']
            === 'critical'
                ? self::FAILURE
                : self::SUCCESS;
    }
}
