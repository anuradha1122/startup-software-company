<?php

namespace App\Services\Tasks;

use App\Models\ProjectTask;
use App\Models\TaskWorkflowStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TaskWorkflowService
{
    public function move(
        ProjectTask $task,
        TaskWorkflowStatus $status,
        User $user,
        ?string $notes = null
    ): ProjectTask {
        if (! $status->active) {
            throw new RuntimeException(
                'Inactive workflow statuses cannot be used.'
            );
        }

        if (
            $task->workflow_status_id
            === $status->id
        ) {
            return $task;
        }

        if (
            $status->is_completed
            && $task->submissions()
                ->where(
                    'status',
                    'approved'
                )
                ->doesntExist()
        ) {
            throw new RuntimeException(
                'A task cannot be completed until a work submission is approved.'
            );
        }

        if (
            $status->is_in_progress
            && $this->hasIncompleteDependencies(
                $task
            )
        ) {
            throw new RuntimeException(
                'This task has incomplete dependencies.'
            );
        }

        return DB::transaction(
            function () use (
                $task,
                $status,
                $user,
                $notes
            ): ProjectTask {
                $fromStatusId =
                    $task->workflow_status_id;

                $updates = [
                    'workflow_status_id' => $status->id,
                ];

                if (
                    $status->is_in_progress
                    && ! $task->started_at
                ) {
                    $updates['started_at'] =
                        now();
                }

                if (
                    $status->is_completed
                ) {
                    $updates['completed_at'] =
                        now();
                } elseif (
                    $task->completed_at
                ) {
                    $updates['completed_at'] =
                        null;
                }

                $task->update($updates);

                $task
                    ->transitions()
                    ->create([
                        'from_status_id' => $fromStatusId,

                        'to_status_id' => $status->id,

                        'changed_by' => $user->id,

                        'notes' => $notes,

                        'changed_at' => now(),
                    ]);

                return $task->refresh();
            }
        );
    }

    private function hasIncompleteDependencies(
        ProjectTask $task
    ): bool {
        return $task
            ->dependencies()
            ->whereHas(
                'workflowStatus',
                fn ($query) => $query->where(
                    'is_completed',
                    false
                )
            )
            ->exists();
    }
}
