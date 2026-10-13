<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MoveProjectTaskRequest;
use App\Models\ProjectTask;
use App\Models\TaskWorkflowStatus;
use App\Services\Tasks\TaskWorkflowService;
use Illuminate\Http\RedirectResponse;

class ProjectTaskWorkflowController extends Controller
{
    public function move(
        MoveProjectTaskRequest $request,
        ProjectTask $task,
        TaskWorkflowService $workflow
    ): RedirectResponse {
        abort_unless(
            in_array(
                $task->project->status,
                [
                    'active',
                    'on_hold',
                ],
                true
            ),
            409
        );

        $status =
            TaskWorkflowStatus::findOrFail(
                $request->integer(
                    'workflow_status_id'
                )
            );

        $workflow->move(
            $task,
            $status,
            $request->user(),
            $request->validated(
                'notes'
            )
        );

        return back()->with(
            'success',
            'Task workflow updated.'
        );
    }
}
