<?php

namespace App\Services\Reports;

use App\Models\ReportRun;
use App\Models\User;
use Throwable;

class ReportRunService
{
    public function completed(
        string $type,
        string $format,
        User $user,
        string $downloadName,
        ?int $rowCount = null,
        array $filters = []
    ): ReportRun {
        return ReportRun::create([
            'report_type' => $type,

            'format' => $format,

            'status' => 'completed',

            'filters' => $filters === []
                    ? null
                    : $filters,

            'download_name' => $downloadName,

            'row_count' => $rowCount,

            'generated_by' => $user->id,

            'generated_at' => now(),
        ]);
    }

    public function failed(
        string $type,
        string $format,
        User $user,
        Throwable $exception,
        array $filters = []
    ): ReportRun {
        return ReportRun::create([
            'report_type' => $type,

            'format' => $format,

            'status' => 'failed',

            'filters' => $filters === []
                    ? null
                    : $filters,

            'failure_message' => str(
                $exception->getMessage()
            )->limit(5000),

            'generated_by' => $user->id,

            'generated_at' => now(),
        ]);
    }
}
