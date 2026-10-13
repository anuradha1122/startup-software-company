<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRiskRequest;
use App\Models\Project;
use App\Models\ProjectRisk;
use App\Models\User;
use App\Notifications\ProjectRiskEscalatedNotification;
use App\Services\Projects\ProjectRiskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ProjectRiskController extends Controller
{
    public function store(
        StoreProjectRiskRequest $request,
        Project $project,
        ProjectRiskService $service
    ): RedirectResponse {
        $service->create(
            $project,
            $request->validated()
        );

        return back()->with(
            'success',
            'Risk added.'
        );
    }

    public function escalate(
        Request $request,
        Project $project,
        ProjectRisk $risk
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'projects.risks.escalate'
            ),
            403
        );

        abort_unless(
            $risk->project_id
                === $project->id,
            404
        );

        $risk->update([
            'escalated_at' => now(),
        ]);

        $project->update([
            'health' => $risk->score >= 20
                    ? 'red'
                    : 'amber',
        ]);

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
                ->unique('id');

        Notification::send(
            $recipients,
            new ProjectRiskEscalatedNotification(
                $risk->load('project')
            )
        );

        return back()->with(
            'success',
            'Risk escalated.'
        );
    }

    public function close(
        Request $request,
        Project $project,
        ProjectRisk $risk
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'projects.risks.manage'
            ),
            403
        );

        abort_unless(
            $risk->project_id
                === $project->id,
            404
        );

        $risk->update([
            'status' => 'closed',

            'closed_at' => now(),
        ]);

        return back()->with(
            'success',
            'Risk closed.'
        );
    }
}
