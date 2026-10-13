<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectMilestoneRequest;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectMilestoneController extends Controller
{
    public function store(
        StoreProjectMilestoneRequest $request,
        Project $project
    ): RedirectResponse {
        $phaseId =
            $request->validated(
                'project_phase_id'
            );

        if ($phaseId) {
            abort_unless(
                $project
                    ->phases()
                    ->whereKey(
                        $phaseId
                    )
                    ->exists(),
                422
            );
        }

        $project
            ->milestones()
            ->create([
                ...$request->validated(),

                'status' => 'planned',
            ]);

        return back()->with(
            'success',
            'Milestone created.'
        );
    }

    public function complete(
        Request $request,
        Project $project,
        ProjectMilestone $milestone
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'projects.milestones.manage'
            ),
            403
        );

        abort_unless(
            $milestone->project_id
                === $project->id,
            404
        );

        $milestone->update([
            'status' => 'completed',

            'completed_at' => now(),
        ]);

        return back()->with(
            'success',
            'Milestone completed.'
        );
    }
}
