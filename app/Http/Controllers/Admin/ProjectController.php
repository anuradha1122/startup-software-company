<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\CompanySetting;
use App\Models\Contract;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectCreatedNotification;
use App\Notifications\ProjectKickoffNotification;
use App\Services\Projects\ProjectSetupService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ProjectController extends Controller
{
    public function index(
        Request $request
    ): Response {
        Gate::authorize(
            'viewAny',
            Project::class
        );

        $projects =
            Project::query()
                ->with([
                    'client',
                    'projectManager',
                ])
                ->when(
                    $request->filled('search'),
                    function ($query) use (
                        $request
                    ): void {
                        $search =
                            (string) $request->string(
                                'search'
                            );

                        $query->where(
                            function ($inner) use (
                                $search
                            ): void {
                                $inner
                                    ->where(
                                        'project_number',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $request->filled('status'),
                    fn ($query) => $query->where(
                        'status',
                        $request->string(
                            'status'
                        )
                    )
                )
                ->latest()
                ->paginate(20)
                ->withQueryString();

        return Inertia::render(
            'admin/projects/index',
            [
                'projects' => $projects,

                'filters' => $request->only([
                    'search',
                    'status',
                ]),
            ]
        );
    }

    public function create(): Response
    {
        Gate::authorize(
            'create',
            Project::class
        );

        $contracts =
            Contract::query()
                ->where(
                    'status',
                    'signed'
                )
                ->whereDoesntHave(
                    'project'
                )
                ->with([
                    'client.contacts',
                ])
                ->latest()
                ->get();

        return Inertia::render(
            'admin/projects/create',
            [
                'contracts' => $contracts,

                'projectManagers' => User::query()
                    ->where(
                        'status',
                        'active'
                    )
                    ->whereHas(
                        'roles',
                        fn ($query) => $query->whereIn(
                            'name',
                            [
                                'Project Manager',
                                'Director',
                                'Admin',
                            ]
                        )
                    )
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name',
                        'email',
                    ]),
            ]
        );
    }

    public function store(
        StoreProjectRequest $request,
        ProjectSetupService $service
    ): RedirectResponse {
        $contract =
            Contract::findOrFail(
                $request->integer(
                    'contract_id'
                )
            );

        $sponsorId =
            $request->validated(
                'client_sponsor_contact_id'
            );

        if ($sponsorId) {
            abort_unless(
                $contract
                    ->client
                    ->contacts()
                    ->whereKey(
                        $sponsorId
                    )
                    ->exists(),
                422,
                'The sponsor contact does not belong to the contract client.'
            );
        }

        $project =
            $service->create(
                $contract,
                $request->validated(),
                $request->user()->id
            );

        $project->load([
            'projectManager',
        ]);

        $recipients =
            User::role([
                'Director',
                'Admin',
            ])
                ->where(
                    'status',
                    'active'
                )
                ->get()
                ->push(
                    $project->projectManager
                )
                ->unique('id');

        Notification::send(
            $recipients,
            new ProjectCreatedNotification(
                $project
            )
        );

        return redirect()
            ->route(
                'admin.projects.show',
                $project
            )
            ->with(
                'success',
                'Project created.'
            );
    }

    public function show(
        Project $project
    ): Response {
        Gate::authorize(
            'view',
            $project
        );

        $project->load([
            'client.contacts',
            'contract',
            'requirementVersion.requirement',
            'estimate',
            'projectManager',
            'clientSponsor',

            'charters' => fn ($query) => $query->orderByDesc(
                'version_number'
            ),

            'approvedCharter',

            'phases.milestones',

            'milestones',

            'stakeholders',

            'risks.owner',

            'decisions.decidedBy',
        ]);

        return Inertia::render(
            'admin/projects/show',
            [
                'project' => $project,

                'users' => User::query()
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name',
                        'email',
                    ]),
            ]
        );
    }

    public function update(
        UpdateProjectRequest $request,
        Project $project
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $project
        );

        $sponsorId =
            $request->validated(
                'client_sponsor_contact_id'
            );

        if ($sponsorId) {
            abort_unless(
                $project
                    ->client
                    ->contacts()
                    ->whereKey(
                        $sponsorId
                    )
                    ->exists(),
                422
            );
        }

        $project->update(
            $request->validated()
        );

        return back()->with(
            'success',
            'Project updated.'
        );
    }

    public function activate(
        Request $request,
        Project $project
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'projects.activate'
            ),
            403
        );

        abort_unless(
            $project->status
                === 'planned',
            409
        );

        abort_unless(
            $project->approved_charter_id,
            422,
            'The project charter must be approved before activation.'
        );

        $project->update([
            'status' => 'active',

            'actual_start_date' => $project->actual_start_date
                ?? now()->toDateString(),

            'activated_at' => now(),
        ]);

        return back()->with(
            'success',
            'Project activated.'
        );
    }

    public function hold(
        Request $request,
        Project $project
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'projects.update'
            ),
            403
        );

        abort_unless(
            $project->status
                === 'active',
            409
        );

        $project->update([
            'status' => 'on_hold',
        ]);

        return back()->with(
            'success',
            'Project placed on hold.'
        );
    }

    public function resume(
        Request $request,
        Project $project
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'projects.update'
            ),
            403
        );

        abort_unless(
            $project->status
                === 'on_hold',
            409
        );

        $project->update([
            'status' => 'active',
        ]);

        return back()->with(
            'success',
            'Project resumed.'
        );
    }

    public function complete(
        Request $request,
        Project $project
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'projects.complete'
            ),
            403
        );

        abort_unless(
            in_array(
                $project->status,
                [
                    'active',
                    'on_hold',
                ],
                true
            ),
            409
        );

        $project->update([
            'status' => 'completed',

            'actual_end_date' => now()->toDateString(),

            'completed_at' => now(),
        ]);

        return back()->with(
            'success',
            'Project completed.'
        );
    }

    public function kickoff(
        Request $request,
        Project $project
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'projects.kickoff.send'
            ),
            403
        );

        abort_unless(
            $project->status
                === 'active',
            409,
            'Kickoff email requires an active project.'
        );

        $contact =
            $project->clientSponsor
            ?? $project
                ->client
                ->contacts()
                ->where(
                    'is_primary',
                    true
                )
                ->whereNotNull('email')
                ->first();

        abort_unless(
            $contact
                && $contact->email,
            422,
            'No client sponsor or primary contact email is available.'
        );

        $project->load(
            'projectManager'
        );

        Notification::route(
            'mail',
            [
                $contact->email => $contact->name,
            ]
        )->notify(
            new ProjectKickoffNotification(
                $project
            )
        );

        return back()->with(
            'success',
            'Project kickoff email queued.'
        );
    }

    public function charterPdf(
        Request $request,
        Project $project
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'projects.export'
            ),
            403
        );

        abort_unless(
            $project->approvedCharter,
            422
        );

        $project->load([
            'client',
            'contract',
            'requirementVersion.requirement',
            'projectManager',
            'clientSponsor',
            'approvedCharter',
            'phases',
            'milestones',
            'stakeholders',
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
                'Project charter PDF generated'
            );

        return Pdf::loadView(
            'pdf.project-charter',
            [
                'project' => $project,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            "{$project->project_number}-charter.pdf"
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'projects.export'
            ),
            403
        );

        return Excel::download(
            new ProjectsExport,
            'projects.xlsx'
        );
    }
}
