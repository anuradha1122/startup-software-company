<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRequirementRequest;
use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Requirement;
use App\Models\RequirementVersion;
use App\Models\User;
use App\Services\DocumentNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class RequirementController extends Controller
{
    public function index(
        Request $request
    ): Response {
        Gate::authorize(
            'viewAny',
            Requirement::class
        );

        $requirements =
            Requirement::query()
                ->with([
                    'client',
                    'owner',
                    'latestVersion',
                ])
                ->when(
                    $request->filled(
                        'search'
                    ),
                    function (
                        $query
                    ) use ($request): void {
                        $search =
                            $request->string(
                                'search'
                            );

                        $query->where(
                            function (
                                $inner
                            ) use ($search): void {
                                $inner
                                    ->where(
                                        'requirement_number',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'title',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $request->filled(
                        'status'
                    ),
                    fn ($query) => $query->where(
                        'status',
                        $request->string(
                            'status'
                        )
                    )
                )
                ->when(
                    $request->filled(
                        'client_id'
                    ),
                    fn ($query) => $query->where(
                        'client_id',
                        $request->integer(
                            'client_id'
                        )
                    )
                )
                ->latest()
                ->paginate(20)
                ->withQueryString();

        return Inertia::render(
            'admin/requirements/index',
            [
                'requirements' => $requirements,

                'clients' => $this->clients(),

                'filters' => $request->only([
                    'search',
                    'status',
                    'client_id',
                ]),
            ]
        );
    }

    public function create(): Response
    {
        Gate::authorize(
            'create',
            Requirement::class
        );

        return Inertia::render(
            'admin/requirements/create',
            [
                'clients' => $this->clients(),

                'owners' => $this->owners(),
            ]
        );
    }

    public function store(
        StoreRequirementRequest $request,
        DocumentNumberService $numbers
    ): RedirectResponse {
        $validated =
            $request->validated();

        $requirement =
            DB::transaction(
                function () use (
                    $request,
                    $validated,
                    $numbers
                ): Requirement {
                    $requirement =
                        Requirement::create([
                            'requirement_number' => $numbers->next(
                                'requirement'
                            ),

                            'client_id' => $validated[
                                    'client_id'
                                ],

                            'title' => $validated[
                                    'title'
                                ],

                            'owner_id' => $validated[
                                    'owner_id'
                                ] ?? null,

                            'status' => 'draft',

                            'created_by' => $request->user()
                                ->id,
                        ]);

                    $requirement
                        ->versions()
                        ->create([
                            'version_number' => 1,

                            'status' => 'draft',

                            'version_title' => 'Initial Requirements',

                            'objective' => $validated[
                                    'objective'
                                ] ?? null,

                            'scope_in' => $validated[
                                    'scope_in'
                                ] ?? null,

                            'scope_out' => $validated[
                                    'scope_out'
                                ] ?? null,

                            'functional_requirements' => $this->cleanArray(
                                $validated[
                                    'functional_requirements'
                                ] ?? []
                            ),

                            'non_functional_requirements' => $this->cleanArray(
                                $validated[
                                    'non_functional_requirements'
                                ] ?? []
                            ),

                            'assumptions' => $this->cleanArray(
                                $validated[
                                    'assumptions'
                                ] ?? []
                            ),

                            'constraints' => $this->cleanArray(
                                $validated[
                                    'constraints'
                                ] ?? []
                            ),

                            'acceptance_criteria' => $this->cleanArray(
                                $validated[
                                    'acceptance_criteria'
                                ] ?? []
                            ),

                            'client_notes' => $validated[
                                    'client_notes'
                                ] ?? null,

                            'internal_notes' => $validated[
                                    'internal_notes'
                                ] ?? null,

                            'created_by' => $request->user()
                                ->id,
                        ]);

                    return $requirement;
                }
            );

        return redirect()
            ->route(
                'admin.requirements.show',
                $requirement
            )
            ->with(
                'success',
                'Requirement created.'
            );
    }

    public function show(
        Requirement $requirement
    ): Response {
        Gate::authorize(
            'view',
            $requirement
        );

        $requirement->load([
            'client.contacts',
            'owner',

            'versions' => fn ($query) => $query
                ->with([
                    'approvals',
                    'attachments',
                    'estimates.lines',
                ])
                ->orderByDesc(
                    'version_number'
                ),

            'approvedVersion',
        ]);

        return Inertia::render(
            'admin/requirements/show',
            [
                'requirement' => $requirement,
            ]
        );
    }

    public function srsPdf(
        Request $request,
        Requirement $requirement,
        RequirementVersion $version
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'requirements.export'
            ),
            403
        );

        abort_unless(
            $version->requirement_id
                === $requirement->id,
            404
        );

        $version->load([
            'requirement.client',
            'approvals',
        ]);

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $requirement
            )
            ->event('export')
            ->withProperties([
                'version' => $version->version_number,
            ])
            ->log(
                'Requirement SRS PDF generated'
            );

        return Pdf::loadView(
            'pdf.requirement-srs',
            [
                'requirement' => $requirement,

                'version' => $version,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )
            ->setPaper('a4')
            ->download(
                "{$requirement->requirement_number}-v{$version->version_number}.pdf"
            );
    }

    private function clients()
    {
        return Client::query()
            ->where(
                'status',
                'active'
            )
            ->orderBy('name')
            ->get([
                'id',
                'public_id',
                'client_number',
                'name',
            ]);
    }

    private function owners()
    {
        return User::query()
            ->where(
                'status',
                'active'
            )
            ->whereHas(
                'roles',
                fn ($query) => $query->whereIn(
                    'name',
                    [
                        'Director',
                        'Admin',
                        'Project Manager',
                    ]
                )
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'email',
            ]);
    }

    private function cleanArray(
        array $values
    ): array {
        return array_values(
            array_filter(
                array_map(
                    fn ($value) => trim(
                        (string) $value
                    ),
                    $values
                ),
                fn ($value) => $value !== ''
            )
        );
    }
}
