<?php

namespace App\Http\Controllers\Admin;

use App\Exports\QuotationExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecordCommercialDecisionRequest;
use App\Http\Requests\Admin\StoreQuotationRequest;
use App\Http\Requests\Admin\UpdateQuotationRequest;
use App\Models\CompanySetting;
use App\Models\Estimate;
use App\Models\Proposal;
use App\Models\Quotation;
use App\Models\RequirementVersion;
use App\Models\User;
use App\Notifications\CommercialDocumentAcceptedNotification;
use App\Notifications\QuotationSentNotification;
use App\Services\Commercial\CommercialSourceValidator;
use App\Services\Commercial\QuotationCalculatorService;
use App\Services\DocumentNumberService;
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

class QuotationController extends Controller
{
    public function index(): Response
    {
        Gate::authorize(
            'viewAny',
            Quotation::class
        );

        return Inertia::render(
            'admin/quotations/index',
            [
                'quotations' => Quotation::with(
                    'client'
                )
                    ->latest()
                    ->paginate(20),
            ]
        );
    }

    public function create(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'quotations.create'
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
            'admin/quotations/create',
            [
                'versions' => $versions,

                'proposal' => $request->filled(
                    'proposal'
                )
                        ? Proposal::where(
                            'public_id',
                            $request->string(
                                'proposal'
                            )
                        )->first()
                        : null,
            ]
        );
    }

    public function store(
        StoreQuotationRequest $request,
        DocumentNumberService $numbers,
        CommercialSourceValidator $validator,
        QuotationCalculatorService $calculator
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

        $proposal = null;

        if (
            $request->filled(
                'proposal_id'
            )
        ) {
            $proposal =
                Proposal::findOrFail(
                    $request->integer(
                        'proposal_id'
                    )
                );

            abort_unless(
                $proposal->client_id
                    === $version
                        ->requirement
                        ->client_id,
                422
            );
        }

        $quotation =
            Quotation::create([
                ...$request->safe()->except([
                    'proposal_id',
                    'requirement_version_id',
                    'estimate_id',
                ]),

                'quotation_number' => $numbers->next(
                    'quotation'
                ),

                'proposal_id' => $proposal?->id,

                'client_id' => $version
                    ->requirement
                    ->client_id,

                'requirement_version_id' => $version->id,

                'estimate_id' => $estimate->id,

                'status' => 'draft',

                'created_by' => $request->user()->id,
            ]);

        $calculator->calculate(
            $quotation
        );

        return redirect()
            ->route(
                'admin.quotations.show',
                $quotation
            );
    }

    public function show(
        Quotation $quotation
    ): Response {
        Gate::authorize(
            'view',
            $quotation
        );

        $quotation->load([
            'client.contacts',
            'proposal',
            'requirementVersion.requirement',
            'estimate.lines',
            'contract',
        ]);

        return Inertia::render(
            'admin/quotations/show',
            [
                'quotation' => $quotation,
            ]
        );
    }

    public function update(
        UpdateQuotationRequest $request,
        Quotation $quotation,
        QuotationCalculatorService $calculator
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $quotation
        );

        $quotation->update(
            $request->safe()->except([
                'proposal_id',
                'requirement_version_id',
                'estimate_id',
            ])
        );

        $calculator->calculate(
            $quotation
        );

        return back()->with(
            'success',
            'Quotation updated.'
        );
    }

    public function approve(
        Request $request,
        Quotation $quotation
    ): RedirectResponse {
        Gate::authorize(
            'approve',
            $quotation
        );

        $quotation->update([
            'status' => 'approved',

            'approved_by' => $request->user()->id,

            'approved_at' => now(),
        ]);

        return back()->with(
            'success',
            'Quotation approved.'
        );
    }

    public function send(
        Request $request,
        Quotation $quotation
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'quotations.send'
            ),
            403
        );

        abort_unless(
            $quotation->status
                === 'approved',
            409
        );

        $contact =
            $quotation
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
            422
        );

        $quotation->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        Notification::route(
            'mail',
            [
                $contact->email => $contact->name,
            ]
        )->notify(
            new QuotationSentNotification(
                $quotation
            )
        );

        return back()->with(
            'success',
            'Quotation sent.'
        );
    }

    public function decision(
        RecordCommercialDecisionRequest $request,
        Quotation $quotation
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'quotations.record-decision'
            ),
            403
        );

        abort_unless(
            $quotation->status
                === 'sent',
            409
        );

        $decision =
            $request->validated(
                'decision'
            );

        $quotation->update([
            'status' => $decision,

            'responded_at' => now(),

            'decision_notes' => $request->validated(
                'decision_notes'
            ),
        ]);

        if ($decision === 'accepted') {
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
                    'Quotation',
                    $quotation->quotation_number,
                    "/admin/quotations/{$quotation->public_id}"
                )
            );
        }

        return back()->with(
            'success',
            'Quotation decision recorded.'
        );
    }

    public function excel(
        Request $request,
        Quotation $quotation
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'quotations.export'
            ),
            403
        );

        return Excel::download(
            new QuotationExport(
                $quotation
            ),
            "{$quotation->quotation_number}.xlsx"
        );
    }

    public function pdf(
        Request $request,
        Quotation $quotation
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'quotations.export'
            ),
            403
        );

        $quotation->load([
            'client',
            'estimate.lines',
            'requirementVersion.requirement',
        ]);

        return Pdf::loadView(
            'pdf.quotation',
            [
                'quotation' => $quotation,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            "{$quotation->quotation_number}.pdf"
        );
    }
}
