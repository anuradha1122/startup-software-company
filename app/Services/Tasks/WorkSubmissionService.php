<?php

namespace App\Services\Tasks;

use App\Models\ProjectTask;
use App\Models\TaskWorkflowStatus;
use App\Models\User;
use App\Models\WorkSubmission;
use Illuminate\Support\Facades\DB;

class WorkSubmissionService
{
    public function submit(
        ProjectTask $task,
        array $data,
        User $user
    ): WorkSubmission {
        return DB::transaction(
            function () use (
                $task,
                $data,
                $user
            ): WorkSubmission {
                $latest =
                    $task
                        ->submissions()
                        ->lockForUpdate()
                        ->orderByDesc(
                            'version_number'
                        )
                        ->first();

                if (
                    $latest
                    && $latest->status
                    === 'submitted'
                ) {
                    abort(
                        409,
                        'A submission is already awaiting review.'
                    );
                }

                $version =
                    ($latest?->version_number ?? 0)
                    + 1;

                if ($latest) {
                    $latest->update([
                        'status' => 'superseded',
                    ]);
                }

                $submission =
                    $task
                        ->submissions()
                        ->create([
                            'version_number' => $version,

                            'status' => 'submitted',

                            'summary' => $data['summary'],

                            'testing_notes' => $data[
                                    'testing_notes'
                                ] ?? null,

                            'deployment_notes' => $data[
                                    'deployment_notes'
                                ] ?? null,

                            'reference_url' => $data[
                                    'reference_url'
                                ] ?? null,

                            'submitted_by' => $user->id,

                            'submitted_at' => now(),
                        ]);

                $reviewStatus =
                    TaskWorkflowStatus::query()
                        ->where(
                            'is_review',
                            true
                        )
                        ->where(
                            'active',
                            true
                        )
                        ->firstOrFail();

                $from =
                    $task
                        ->workflow_status_id;

                $task->update([
                    'workflow_status_id' => $reviewStatus->id,
                ]);

                $task
                    ->transitions()
                    ->create([
                        'from_status_id' => $from,

                        'to_status_id' => $reviewStatus->id,

                        'changed_by' => $user->id,

                        'notes' => "Work submission v{$version} submitted.",

                        'changed_at' => now(),
                    ]);

                return $submission;
            }
        );
    }
}
