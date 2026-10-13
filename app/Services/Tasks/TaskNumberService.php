<?php

namespace App\Services\Tasks;

use App\Models\Project;
use Illuminate\Support\Facades\DB;

class TaskNumberService
{
    public function next(
        Project $project
    ): array {
        return DB::transaction(
            function () use (
                $project
            ): array {
                $sequence =
                    (int) $project
                        ->tasks()
                        ->withTrashed()
                        ->lockForUpdate()
                        ->max(
                            'task_sequence'
                        )
                    + 1;

                return [
                    'sequence' => $sequence,

                    'number' => sprintf(
                        '%s-T%04d',
                        $project->project_number,
                        $sequence
                    ),
                ];
            },
            3
        );
    }
}
