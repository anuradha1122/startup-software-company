<?php

namespace App\Services\People;

use App\Models\Project;
use App\Models\ProjectAssignment;
use Carbon\CarbonInterface;
use RuntimeException;

class AssignmentEligibilityService
{
    public function activeAssignment(
        Project $project,
        int $userId
    ): ?ProjectAssignment {
        return $this->assignmentForDate(
            $project,
            $userId,
            today()
        );
    }

    public function assignmentForDate(
        Project $project,
        int $userId,
        CarbonInterface $date
    ): ?ProjectAssignment {
        return $project
            ->assignments()
            ->where(
                'user_id',
                $userId
            )
            ->whereIn(
                'status',
                [
                    'active',
                    'ended',
                ]
            )
            ->where(function ($query) use (
                $date
            ): void {
                $query
                    ->whereNull(
                        'start_date'
                    )
                    ->orWhereDate(
                        'start_date',
                        '<=',
                        $date->toDateString()
                    );
            })
            ->where(function ($query) use (
                $date
            ): void {
                $query
                    ->whereNull(
                        'end_date'
                    )
                    ->orWhereDate(
                        'end_date',
                        '>=',
                        $date->toDateString()
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
