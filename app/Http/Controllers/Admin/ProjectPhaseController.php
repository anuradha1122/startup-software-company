<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectPhaseRequest;
use App\Models\Project;
use App\Models\ProjectPhase;
use Illuminate\Http\RedirectResponse;

class ProjectPhaseController extends Controller
{
    public function store(
        StoreProjectPhaseRequest $request,
        Project $project
    ): RedirectResponse {
        $sortOrder =
            ($project
                ->phases()
                ->max('sort_order') ?? 0)
            + 1;

        $project
            ->phases()
            ->create([
                ...$request->validated(),

                'sort_order' => $sortOrder,

                'status' => 'planned',
            ]);

        return back()->with(
            'success',
            'Project phase added.'
        );
    }

    public function destroy(
        Project $project,
        ProjectPhase $phase
    ): RedirectResponse {
        abort_unless(
            request()->user()->can(
                'projects.phases.manage'
            ),
            403
        );

        abort_unless(
            $phase->project_id
                === $project->id,
            404
        );

        abort_if(
            $phase->milestones()->exists(),
            409,
            'Remove or move milestones before deleting this phase.'
        );

        $phase->delete();

        return back()->with(
            'success',
            'Project phase deleted.'
        );
    }
}
