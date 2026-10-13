<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectAssignmentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectAssignmentRequest;
use App\Http\Requests\Admin\UpdateProjectAssignmentRequest;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\User;
use App\Notifications\ProjectAssignmentCreatedNotification;
use App\Notifications\ProjectAssignmentEndedNotification;
use App\Services\People\ProjectAssignmentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ProjectAssignmentController extends Controller
{
    public function index(
        Request $request,
        Project $project
    ): Response {
        abort_unless(
            $request->user()->can(
                'project-assignments.view'
            ),
            403
        );

        $project->load([
            'client',
            'projectManager',
            'assignments.user.resourceProfile.skills',
        ]);

        $assignedUserIds =
            $project
                ->assignments
                ->pluck('user_id');

        $availableUsers =
            User::query()
                ->where(
                    'status',
                    'active'
                )
                ->whereNotIn(
                    'id',
                    $assignedUserIds
                )
                ->where(function ($query): void {
                    $query
                        ->whereDoesntHave(
                            'resourceProfile'
                        )
                        ->orWhereHas(
                            'resourceProfile',
                            fn ($profile) => $profile->where(
                                'available_for_projects',
                                true
                            )
                        );
                })
                ->with(
                    'resourceProfile.skills'
                )
                ->orderBy('name')
                ->get();

        return Inertia::render(
            'admin/projects/team/index',
            [
                'project' => $project,

                'availableUsers' => $availableUsers,
            ]
        );
    }

    public function store(
        StoreProjectAssignmentRequest $request,
        Project $project,
        ProjectAssignmentService $service
    ): RedirectResponse {
        $user =
            User::findOrFail(
                $request->integer(
                    'user_id'
                )
            );

        if (
            $request->validated(
                'status'
            ) === 'active'
        ) {
            abort_unless(
                $project->status
                === 'active',
                422,
                'An active assignment requires an active project.'
            );
        }

        $data =
            $request->safe()->except(
                'user_id'
            );

        $assignment =
            $service->create(
                $project,
                $user,
                $data,
                $request->user()->id
            );

        $user->notify(
            new ProjectAssignmentCreatedNotification(
                $assignment->load(
                    'project'
                )
            )
        );

        return back()->with(
            'success',
            'Project assignment created.'
        );
    }

    public function update(
        UpdateProjectAssignmentRequest $request,
        Project $project,
        ProjectAssignment $assignment
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $assignment
        );

        $this->assertBelongs(
            $project,
            $assignment
        );

        if (
            $request->validated(
                'status'
            ) === 'active'
        ) {
            abort_unless(
                $project->status
                === 'active',
                422
            );
        }

        $assignment->update(
            $request->validated()
        );

        return back()->with(
            'success',
            'Project assignment updated.'
        );
    }

    public function end(
        Request $request,
        Project $project,
        ProjectAssignment $assignment
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'project-assignments.end'
            ),
            403
        );

        $this->assertBelongs(
            $project,
            $assignment
        );

        abort_unless(
            in_array(
                $assignment->status,
                [
                    'planned',
                    'active',
                ],
                true
            ),
            409
        );

        if (
            $assignment->user_id
            === $project->project_manager_id
            && ! in_array(
                $project->status,
                [
                    'completed',
                    'cancelled',
                ],
                true
            )
        ) {
            abort(
                409,
                'The current project manager assignment cannot be ended while the project is open.'
            );
        }

        $activeTasks =
            $project
                ->tasks()
                ->where(
                    'assignee_id',
                    $assignment->user_id
                )
                ->whereHas(
                    'workflowStatus',
                    fn ($query) => $query->where(
                        'is_completed',
                        false
                    )
                )
                ->count();

        abort_if(
            $activeTasks > 0,
            409,
            'Reassign or complete this user’s open tasks before ending the project assignment.'
        );

        $assignment->update([
            'status' => 'ended',

            'end_date' => $assignment->end_date
                ?? today(),

            'ended_by' => $request->user()->id,

            'ended_at' => now(),
        ]);

        $assignment
            ->user
            ->notify(
                new ProjectAssignmentEndedNotification(
                    $assignment->load(
                        'project'
                    )
                )
            );

        return back()->with(
            'success',
            'Project assignment ended.'
        );
    }

    public function excel(
        Request $request,
        Project $project
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'project-assignments.export'
            ),
            403
        );

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $project
            )
            ->event('export')
            ->log(
                'Project assignment Excel generated'
            );

        return Excel::download(
            new ProjectAssignmentsExport(
                $project
            ),
            "{$project->project_number}-team.xlsx"
        );
    }

    public function pdf(
        Request $request,
        Project $project
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'project-assignments.export'
            ),
            403
        );

        $project->load([
            'client',
            'projectManager',
            'assignments.user.resourceProfile.skills',
        ]);

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $project
            )
            ->event('export')
            ->log(
                'Project team PDF generated'
            );

        return Pdf::loadView(
            'pdf.project-team',
            [
                'project' => $project,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            "{$project->project_number}-team.pdf"
        );
    }

    private function assertBelongs(
        Project $project,
        ProjectAssignment $assignment
    ): void {
        abort_unless(
            $assignment->project_id
            === $project->id,
            404
        );
    }
}
