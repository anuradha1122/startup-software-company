<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LeadsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLeadRequest;
use App\Http\Requests\Admin\UpdateLeadRequest;
use App\Models\CompanySetting;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\User;
use App\Notifications\LeadAssignedNotification;
use App\Services\CRM\LeadConversionService;
use App\Services\DocumentNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class LeadController extends Controller
{
    public function index(
        Request $request
    ): Response {
        Gate::authorize('viewAny', Lead::class);

        $stages = LeadStage::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();

        $leads = Lead::query()
            ->with([
                'stage',
                'source',
                'owner',
                'contacts',
            ])
            ->when(
                $request->filled('source_id'),
                fn ($query) => $query->where(
                    'source_id',
                    $request->integer('source_id')
                )
            )
            ->when(
                $request->filled('owner_id'),
                fn ($query) => $query->where(
                    'owner_id',
                    $request->integer('owner_id')
                )
            )
            ->when(
                $request->filled('search'),
                function ($query) use ($request): void {
                    $search =
                        $request->string('search');

                    $query->where(
                        function ($inner) use (
                            $search
                        ): void {
                            $inner
                                ->where(
                                    'lead_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'title',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'company_name',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->latest()
            ->get();

        return Inertia::render(
            'admin/leads/index',
            [
                'stages' => $stages,
                'leads' => $leads,
                'sources' => $this->sources(),
                'owners' => $this->owners(),
                'filters' => $request->only([
                    'source_id',
                    'owner_id',
                    'search',
                ]),
            ]
        );
    }

    public function create(): Response
    {
        Gate::authorize('create', Lead::class);

        return Inertia::render(
            'admin/leads/create',
            [
                'stages' => $this->stages(),
                'sources' => $this->sources(),
                'owners' => $this->owners(),
            ]
        );
    }

    public function store(
        StoreLeadRequest $request,
        DocumentNumberService $numbers
    ): RedirectResponse {
        $validated = $request->validated();

        $stage = LeadStage::findOrFail(
            $validated['stage_id']
        );

        $lead = DB::transaction(
            function () use (
                $validated,
                $request,
                $numbers,
                $stage
            ): Lead {
                $lead = Lead::create([
                    'lead_number' => $numbers->next('lead'),

                    'title' => $validated['title'],

                    'company_name' => $validated['company_name']
                        ?? null,

                    'stage_id' => $stage->id,

                    'source_id' => $validated['source_id']
                        ?? null,

                    'owner_id' => $validated['owner_id']
                        ?? null,

                    'estimated_value' => $validated['estimated_value']
                        ?? null,

                    'currency' => strtoupper(
                        $validated['currency']
                    ),

                    'probability' => $stage->probability,

                    'priority' => $validated['priority'],

                    'next_follow_up_at' => $validated['next_follow_up_at']
                        ?? null,

                    'notes' => $validated['notes']
                        ?? null,

                    'created_by' => $request->user()->id,
                ]);

                $lead->contacts()->create([
                    ...$validated['contact'],
                    'is_primary' => true,
                ]);

                return $lead;
            }
        );

        if ($lead->owner_id) {
            $lead->owner?->notify(
                new LeadAssignedNotification(
                    $lead
                )
            );
        }

        return redirect()
            ->route(
                'admin.leads.show',
                $lead
            )
            ->with(
                'success',
                'Lead created.'
            );
    }

    public function show(
        Lead $lead
    ): Response {
        Gate::authorize('view', $lead);

        $lead->load([
            'stage',
            'source',
            'owner',
            'contacts',
            'activities' => fn ($query) => $query
                ->with('creator')
                ->latest(),
            'convertedClient',
        ]);

        return Inertia::render(
            'admin/leads/show',
            [
                'lead' => $lead,
            ]
        );
    }

    public function edit(
        Lead $lead
    ): Response {
        Gate::authorize('update', $lead);

        return Inertia::render(
            'admin/leads/edit',
            [
                'lead' => $lead->load([
                    'stage',
                    'source',
                    'owner',
                ]),
                'stages' => $this->stages(),
                'sources' => $this->sources(),
                'owners' => $this->owners(),
            ]
        );
    }

    public function update(
        UpdateLeadRequest $request,
        Lead $lead
    ): RedirectResponse {
        $validated = $request->validated();

        $stage = LeadStage::findOrFail(
            $validated['stage_id']
        );

        $oldOwnerId = $lead->owner_id;
        $oldFollowUp =
            $lead->next_follow_up_at
                ?->toDateTimeString();

        $status = match (true) {
            $stage->is_won => 'won',
            $stage->is_lost => 'lost',
            default => $lead->status === 'converted'
                ? 'converted'
                : 'open',
        };

        $lead->update([
            ...$validated,
            'probability' => $stage->probability,
            'status' => $status,
        ]);

        $newFollowUp =
            $lead->next_follow_up_at
                ?->toDateTimeString();

        if ($oldFollowUp !== $newFollowUp) {
            $lead->updateQuietly([
                'follow_up_reminded_at' => null,
            ]);
        }

        if (
            $oldOwnerId !== $lead->owner_id
            && $lead->owner_id
        ) {
            $lead->owner?->notify(
                new LeadAssignedNotification(
                    $lead
                )
            );
        }

        return redirect()
            ->route(
                'admin.leads.show',
                $lead
            )
            ->with(
                'success',
                'Lead updated.'
            );
    }

    public function updateStage(
        Request $request,
        Lead $lead
    ): RedirectResponse {
        Gate::authorize('update', $lead);

        $validated = $request->validate([
            'stage_id' => [
                'required',
                'exists:lead_stages,id',
            ],
        ]);

        $stage = LeadStage::findOrFail(
            $validated['stage_id']
        );

        $lead->update([
            'stage_id' => $stage->id,
            'probability' => $stage->probability,
            'status' => match (true) {
                $stage->is_won => 'won',
                $stage->is_lost => 'lost',
                default => 'open',
            },
        ]);

        return back();
    }

    public function destroy(
        Lead $lead
    ): RedirectResponse {
        Gate::authorize('delete', $lead);

        if ($lead->status === 'converted') {
            return back()->with(
                'error',
                'Converted leads cannot be archived.'
            );
        }

        $lead->delete();

        return redirect()
            ->route('admin.leads.index')
            ->with(
                'success',
                'Lead archived.'
            );
    }

    public function convert(
        Request $request,
        Lead $lead,
        LeadConversionService $service
    ): RedirectResponse {
        Gate::authorize('convert', $lead);

        $client = $service->convert(
            $lead,
            $request->user()->id
        );

        return back()->with(
            'success',
            "Lead converted to client {$client->name}."
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'leads.export'
            ),
            403
        );

        activity('exports')
            ->causedBy($request->user())
            ->event('export')
            ->log('Lead list exported');

        return Excel::download(
            new LeadsExport(
                $request->only([
                    'source_id',
                    'owner_id',
                    'stage_id',
                ])
            ),
            'leads-'
            .now()->format('Y-m-d-His')
            .'.xlsx'
        );
    }

    public function pipelinePdf(
        Request $request
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'leads.export'
            ),
            403
        );

        $stages = LeadStage::query()
            ->with([
                'leads' => fn ($query) => $query
                    ->with([
                        'owner',
                        'source',
                    ])
                    ->whereIn(
                        'status',
                        [
                            'open',
                            'won',
                            'lost',
                        ]
                    ),
            ])
            ->orderBy('sort_order')
            ->get();

        activity('exports')
            ->causedBy($request->user())
            ->event('export')
            ->log('Lead pipeline PDF generated');

        return Pdf::loadView(
            'pdf.lead-pipeline',
            [
                'stages' => $stages,
                'company' => CompanySetting::first(),
                'generatedBy' => $request->user(),
                'generatedAt' => now(),
            ]
        )
            ->setPaper('a4', 'landscape')
            ->download(
                'lead-pipeline-'
                .now()->format('Y-m-d-His')
                .'.pdf'
            );
    }

    private function stages()
    {
        return LeadStage::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->get();
    }

    private function sources()
    {
        return LeadSource::query()
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    private function owners()
    {
        return User::query()
            ->where('status', 'active')
            ->whereHas(
                'roles',
                fn ($query) => $query->whereIn(
                    'name',
                    [
                        'Director',
                        'Admin',
                        'Sales',
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
}
