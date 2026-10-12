<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreContractRequest;
use App\Models\CompanySetting;
use App\Models\Contract;
use App\Models\Quotation;
use App\Notifications\ContractIssuedNotification;
use App\Services\DocumentNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ContractController extends Controller
{
    public function index(): Response
    {
        Gate::authorize(
            'viewAny',
            Contract::class
        );

        return Inertia::render(
            'admin/contracts/index',
            [
                'contracts' => Contract::with('client')
                    ->latest()
                    ->paginate(20),
            ]
        );
    }

    public function create(
        Quotation $quotation
    ): Response {
        abort_unless(
            request()->user()->can(
                'contracts.create'
            ),
            403
        );

        abort_unless(
            $quotation->status
                === 'accepted',
            409,
            'A contract requires an accepted quotation.'
        );

        abort_if(
            $quotation->contract()->exists(),
            409,
            'This quotation already has a contract.'
        );

        $quotation->load([
            'client',
        ]);

        return Inertia::render(
            'admin/contracts/create',
            [
                'quotation' => $quotation,
            ]
        );
    }

    public function store(
        StoreContractRequest $request,
        DocumentNumberService $numbers
    ): RedirectResponse {
        $quotation =
            Quotation::findOrFail(
                $request->integer(
                    'quotation_id'
                )
            );

        abort_unless(
            $quotation->status
                === 'accepted',
            409
        );

        abort_if(
            $quotation->contract()->exists(),
            409
        );

        $contract =
            Contract::create([
                ...$request->safe()->except([
                    'quotation_id',
                ]),

                'contract_number' => $numbers->next(
                    'contract'
                ),

                'quotation_id' => $quotation->id,

                'client_id' => $quotation->client_id,

                'requirement_version_id' => $quotation
                    ->requirement_version_id,

                'estimate_id' => $quotation->estimate_id,

                'currency' => $quotation->currency,

                'contract_value' => $quotation->total_amount,

                'status' => 'draft',

                'created_by' => $request->user()->id,
            ]);

        return redirect()
            ->route(
                'admin.contracts.show',
                $contract
            );
    }

    public function show(
        Contract $contract
    ): Response {
        Gate::authorize(
            'view',
            $contract
        );

        $contract->load([
            'client.contacts',
            'quotation',
            'requirementVersion.requirement',
            'estimate',
            'documents',
        ]);

        return Inertia::render(
            'admin/contracts/show',
            [
                'contract' => $contract,
            ]
        );
    }

    public function issue(
        Request $request,
        Contract $contract
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'contracts.issue'
            ),
            403
        );

        abort_unless(
            $contract->status
                === 'draft',
            409
        );

        $contact =
            $contract
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

        $contract->update([
            'status' => 'issued',

            'issued_at' => now(),
        ]);

        Notification::route(
            'mail',
            [
                $contact->email => $contact->name,
            ]
        )->notify(
            new ContractIssuedNotification(
                $contract
            )
        );

        return back()->with(
            'success',
            'Contract issued.'
        );
    }

    public function markSigned(
        Request $request,
        Contract $contract
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'contracts.mark-signed'
            ),
            403
        );

        abort_unless(
            $contract->status
                === 'issued',
            409
        );

        $contract->update([
            'status' => 'signed',

            'signed_at' => now(),
        ]);

        return back()->with(
            'success',
            'Contract marked as signed.'
        );
    }

    public function pdf(
        Request $request,
        Contract $contract
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'contracts.export'
            ),
            403
        );

        $contract->load([
            'client',
            'quotation',
            'requirementVersion.requirement',
        ]);

        return Pdf::loadView(
            'pdf.contract',
            [
                'contract' => $contract,

                'company' => CompanySetting::first(),

                'generatedAt' => now(),

                'generatedBy' => $request->user(),
            ]
        )->download(
            "{$contract->contract_number}.pdf"
        );
    }
}
