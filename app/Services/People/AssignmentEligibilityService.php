<?php

namespace App\Services\People;

use App\Models\Project;
use App\Models\ProjectAssignment;
use RuntimeException;

class AssignmentEligibilityService
{
    public function activeAssignment(
        Project $project,
        int $userId
    ): ?ProjectAssignment {
        return $project
            ->assignments()
            ->where(
                'user_id',
                $userId
            )
            ->where(
                'status',
                'active'
            )
            ->where(function ($query): void {
                $query
                    ->whereNull(
                        'start_date'
                    )
                    ->orWhereDate(
                        'start_date',
                        '<=',
                        today()
                    );
            })
            ->where(function ($query): void {
                $query
                    ->whereNull(
                        'end_date'
                    )
                    ->orWhereDate(
                        'end_date',
                        '>=',
                        today()
                    );
            })
            ->first();
    }

    public function assertAssignable(
        Project $project,
        ?int $userId
    ): void {
        if (! $userId) {
            return;
        }

        if (
            ! $this->activeAssignment(
                $project,
                $userId
            )
        ) {
            throw new RuntimeException(
                'The selected user is not an active member of this project.'
            );
        }
    }
}
