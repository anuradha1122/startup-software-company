<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecordCommercialDecisionRequest;
use App\Http\Requests\Admin\StoreProposalRequest;
use App\Http\Requests\Admin\UpdateProposalRequest;
use App\Models\CompanySetting;
use App\Models\Estimate;
use App\Models\Proposal;
use App\Models\RequirementVersion;
use App\Models\User;
use App\Notifications\CommercialDocumentAcceptedNotification;
use App\Notifications\ProposalSentNotification;
use App\Services\Commercial\CommercialSourceValidator;
use App\Services\DocumentNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ProposalController extends Controller
{
    public function index(): Response
    {
        Gate::authorize(
            'viewAny',
            Proposal::class
        );

        return Inertia::render(
            'admin/proposals/index',
            [
                'proposals' => Proposal::with([
                    'client',
                    'requirementVersion.requirement',
                ])
                    ->latest()
                    ->paginate(20),
            ]
        );
    }

    public function create(): Response
    {
        abort_unless(
            request()->user()->can(
                'proposals.create'
            ),
            403
        );

        $versions =
            RequirementVersion::query()
                ->where(
                    'status',
                    'approved'
                )
                ->with([
                    'requirement.client',
                    'estimates' => fn ($query) => $query->where(
                        'status',
                        'approved'
                    ),
                ])
                ->get();

        return Inertia::render(
            'admin/proposals/create',
            [
                'versions' => $versions,
            ]
        );
    }

    public function store(
        StoreProposalRequest $request,
        DocumentNumberService $numbers,
        CommercialSourceValidator $validator
    ): RedirectResponse {
        $version =
            RequirementVersion::with(
                'requirement.client'
            )->findOrFail(
                $request->integer(
                    'requirement_version_id'
                )
            );

        $estimate =
            Estimate::findOrFail(
                $request->integer(
                    'estimate_id'
                )
            );

        $validator->validate(
            $version,
            $estimate
        );

        $proposal = Proposal::create([
            ...$request->safe()->except([
                'requirement_version_id',
                'estimate_id',
            ]),

            'proposal_number' => $numbers->next(
                'proposal'
            ),

            'client_id' => $version
                ->requirement
                ->client_id,

            'requirement_version_id' => $version->id,

            'estimate_id' => $estimate->id,

            'status' => 'draft',

            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route(
                'admin.proposals.show',
                $proposal
            )
            ->with(
                'success',
                'Proposal created.'
            );
    }

    public function show(
        Proposal $proposal
    ): Response {
        Gate::authorize(
            'view',
            $proposal
        );

        $proposal->load([
            'client.contacts',
            'requirementVersion.requirement',
            'estimate.lines',
            'quotation',
        ]);

        return Inertia::render(
            'admin/proposals/show',
            [
                'proposal' => $proposal,
            ]
        );
    }

    public function update(
        UpdateProposalRequest $request,
        Proposal $proposal
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $proposal
        );

        $proposal->update(
            $request->safe()->except([
                'requirement_version_id',
                'estimate_id',
            ])
        );

        return back()->with(
            'success',
            'Proposal updated.'
        );
    }

    public function approve(
        Request $request,
        Proposal $proposal
    ): RedirectResponse {
        Gate::authorize(
            'approve',
            $proposal
        );

        $proposal->update([
            'status' => 'approved',

            'approved_by' => $request->user()->id,

            'approved_at' => now(),
        ]);

        return back()->with(
            'success',
            'Proposal approved.'
        );
    }

    public function send(
        Request $request,
        Proposal $proposal
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'proposals.send'
            ),
            403
        );

        abort_unless(
            $proposal->status
                === 'approved',
            409
        );

        $contact =
            $proposal
                ->client
                ->contacts()
                ->where(
                    'is_primary',
                    true
                )
                ->whereNotNull('email')
                ->first();

        abort_unless(
            $contact,
            422,
            'The client requires a primary contact with an email address.'
        );

        $proposal->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        Notification::route(
            'mail',
            [
                $contact->email => $contact->name,
            ]
        )->notify(
            new ProposalSentNotification(
                $proposal
            )
        );

        return back()->with(
            'success',
            'Proposal sent.'
        );
    }

    public function decision(
        RecordCommercialDecisionRequest $request,
        Proposal $proposal
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'proposals.record-decision'
            ),
            403
        );

        abort_unless(
            $proposal->status
                === 'sent',
            409
        );

        $decision =
            $request->validated(
                'decision'
            );

        $proposal->update([
            'status' => $decision,

            'responded_at' => now(),

            'decision_notes' => $request->validated(
                'decision_notes'
            ),
        ]);

        if ($decision === 'accepted') {
            $this->notifyAccepted(
                $proposal
            );
        }

        return back()->with(
            'success',
            'Proposal decision recorded.'
        );
    }

    public function pdf(
        Request $request,
        Proposal $proposal
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'proposals.export'
            ),
            403
        );

        $proposal->load([
            'client',
            'requirementVersion.requirement',
            'estimate',
        ]);

        return Pdf::loadView(
            'pdf.proposal',
            [
                'proposal' => $proposal,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            "{$proposal->proposal_number}.pdf"
        );
    }

    private function notifyAccepted(
        Proposal $proposal
    ): void {
        $users =
            User::role([
                'Director',
                'Admin',
                'Sales',
            ])
                ->where(
                    'status',
                    'active'
                )
                ->get();

        Notification::send(
            $users,
            new CommercialDocumentAcceptedNotification(
                'Proposal',
                $proposal->proposal_number,
                "/admin/proposals/{$proposal->public_id}"
            )
        );
    }
}
