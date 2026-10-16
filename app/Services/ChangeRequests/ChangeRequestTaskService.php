<?php

namespace App\Services\ChangeRequests;

use App\Models\ChangeRequest;
use App\Models\ProjectTask;
use App\Models\TaskWorkflowStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ChangeRequestTaskService
{
    public function createTask(
        ChangeRequest $changeRequest,
        User $creator
    ): ProjectTask {
        if (
            $changeRequest->status
            !== 'approved'
        ) {
            throw new RuntimeException(
                'Only approved change requests may create delivery work.'
            );
        }

        $project =
            $changeRequest->project;

        return DB::transaction(
            function () use (
                $changeRequest,
                $creator,
                $project
            ): ProjectTask {
                $status =
                    TaskWorkflowStatus::query()
                        ->where(
                            'slug',
                            'backlog'
                        )
                        ->firstOrFail();

                $sequence =
                    (int) $project
                        ->tasks()
                        ->withTrashed()
                        ->lockForUpdate()
                        ->max(
                            'task_sequence'
                        )
                    + 1;

                $taskNumber =
                    sprintf(
                        '%s-T%04d',
                        $project
                            ->project_number,
                        $sequence
                    );

                return $project
                    ->tasks()
                    ->create([
                        'task_sequence' => $sequence,

                        'task_number' => $taskNumber,

                        'workflow_status_id' => $status->id,

                        'title' => "[CR {$changeRequest->change_request_number}] {$changeRequest->title}",

                        'description' => $changeRequest
                            ->description,

                        'type' => 'task',

                        'priority' => $changeRequest
                            ->priority,

                        'created_by' => $creator->id,
                    ]);
            }
        );
    }
}
