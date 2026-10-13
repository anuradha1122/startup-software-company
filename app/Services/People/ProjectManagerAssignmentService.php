<?php

namespace App\Services\People;

use App\Models\Project;

class ProjectManagerAssignmentService
{
    public function sync(
        Project $project,
        int $userId,
        int $createdBy
    ): void {
        $status =
            $project->status === 'active'
                ? 'active'
                : 'planned';

        $assignment =
            $project
                ->assignments()
                ->withTrashed()
                ->where(
                    'user_id',
                    $userId
                )
                ->first();

        if ($assignment) {
            if ($assignment->trashed()) {
                $assignment->restore();
            }

            $assignment->update([
                'role' => 'Project Manager',

                'status' => $status,

                'allocation_percent' => max(
                    1,
                    $assignment
                        ->allocation_percent
                        ?? 100
                ),

                'ended_by' => null,

                'ended_at' => null,
            ]);

            return;
        }

        $project
            ->assignments()
            ->create([
                'user_id' => $userId,

                'role' => 'Project Manager',

                'allocation_percent' => 100,

                'status' => $status,

                'start_date' => $project
                    ->planned_start_date,

                'end_date' => $project
                    ->planned_end_date,

                'billable' => false,

                'created_by' => $createdBy,
            ]);
    }
}
