<?php

namespace App\Services\Support;

use Carbon\CarbonImmutable;

class SupportSlaService
{
    public function deadlines(
        string $priority,
        string $severity
    ): array {
        $now =
            CarbonImmutable::now();

        $firstResponseHours =
            match ($priority) {
                'urgent' => 1,
                'high' => 4,
                'medium' => 8,
                default => 24,
            };

        $resolutionHours =
            match ($severity) {
                'critical' => 8,
                'major' => 24,
                'normal' => 72,
                default => 120,
            };

        return [
            'first_response_due_at' => $now->addHours(
                $firstResponseHours
            ),

            'resolution_due_at' => $now->addHours(
                $resolutionHours
            ),
        ];
    }
}
