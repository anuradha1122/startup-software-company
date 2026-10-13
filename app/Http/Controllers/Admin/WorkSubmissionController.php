<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewWorkSubmissionRequest;
use App\Http\Requests\Admin\StoreWorkSubmissionRequest;
use App\Models\ProjectTask;
use App\Models\TaskWorkflowStatus;
use App\Models\User;
use App\Models\WorkSubmission;
use App\Notifications\WorkSubmissionApprovedNotification;
use App\Notifications\WorkSubmissionChangesRequestedNotification;
use App\Notifications\WorkSubmittedNotification;
use App\Services\Tasks\TaskWorkflowService;
use App\Services\Tasks\WorkSubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class WorkSubmissionController extends Controller
{
    public function store(
        StoreWorkSubmissionRequest $request,
        ProjectTask $task,
        WorkSubmissionService $service
    ): RedirectResponse {
        abort_unless(
            $task->project->status
            === 'active',
            409,
            'Work can only be submitted for an active project.'
        );

        if (
            $task->assignee_id
            && $task->assignee_id
            !== $request->user()->id
            && ! $request->user()->can(
                'work-submissions.review'
            )
        ) {
            abort(
                403,
                'Only the task assignee may submit work.'
            );
        }

        $submission =
            $service->submit(
                $task,
                $request->validated(),
                $request->user()
            );

        foreach (
            $request->file(
                'attachments',
                []
            ) as $file
        ) {
            $path =
                $file->store(
                    "work-submissions/{$submission->public_id}",
                    'local'
                );

            $submission
                ->attachments()
                ->create([
                    'title' => $file
                        ->getClientOriginalName(),

                    'original_name' => $file
                        ->getClientOriginalName(),

                    'disk' => 'local',

                    'path' => $path,

                    'mime_type' => $file->getMimeType(),

                    'size' => $file->getSize(),

                    'uploaded_by' => $request->user()->id,
                ]);
        }

        $project =
            $task
                ->project
                ->load(
                    'projectManager'
                );

        $recipients =
            User::role([
                'Director',
                'Admin',
                'Project Manager',
            ])
                ->where(
                    'status',
                    'active'
                )
                ->get()
                ->push(
                    $project
                        ->projectManager
                )
                ->unique('id')
                ->reject(
                    fn (User $user) => $user->id
                        === $request->user()->id
                );

        Notification::send(
            $recipients,
            new WorkSubmittedNotification(
                $submission->load(
                    'task'
                )
            )
        );

        return back()->with(
            'success',
            'Work submitted for review.'
        );
    }

    public function review(
        ReviewWorkSubmissionRequest $request,
        WorkSubmission $submission,
        TaskWorkflowService $workflow
    ): RedirectResponse {
        abort_unless(
            $submission->status
            === 'submitted',
            409
        );

        $decision =
            $request->validated(
                'decision'
            );

        $task =
            $submission
                ->task()
                ->with([
                    'assignee',
                    'project',
                ])
                ->firstOrFail();

        DB::transaction(
            function () use (
                $request,
                $submission,
                $decision
            ): void {
                $submission->update([
                    'status' => $decision,

                    'reviewed_by' => $request->user()->id,

                    'review_notes' => $request->validated(
                        'review_notes'
                    ),

                    'reviewed_at' => now(),
                ]);
            }
        );

        if (
            $decision
            === 'approved'
        ) {
            $done =
                TaskWorkflowStatus::query()
                    ->where(
                        'is_completed',
                        true
                    )
                    ->where(
                        'active',
                        true
                    )
                    ->firstOrFail();

            $workflow->move(
                $task,
                $done,
                $request->user(),
                "Work submission v{$submission->version_number} approved."
            );

            if ($submission->submitter) {
                $submission
                    ->submitter
                    ->notify(
                        new WorkSubmissionApprovedNotification(
                            $submission
                                ->load('task')
                        )
                    );
            }
        } else {
            $changes =
                TaskWorkflowStatus::query()
                    ->where(
                        'slug',
                        'changes-required'
                    )
                    ->where(
                        'active',
                        true
                    )
                    ->firstOrFail();

            $workflow->move(
                $task,
                $changes,
                $request->user(),
                "Changes requested for submission v{$submission->version_number}."
            );

            if ($submission->submitter) {
                $submission
                    ->submitter
                    ->notify(
                        new WorkSubmissionChangesRequestedNotification(
                            $submission
                                ->load('task')
                        )
                    );
            }
        }

        return back()->with(
            'success',
            $decision === 'approved'
                ? 'Work approved.'
                : 'Changes requested.'
        );
    }
}
