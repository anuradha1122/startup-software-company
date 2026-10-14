<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InvoicesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceRequest;
use App\Http\Requests\Admin\UpdateInvoiceRequest;
use App\Models\ClientContact;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Services\Billing\BillableExpenseImportService;
use App\Services\Billing\InvoiceIssueService;
use App\Services\DocumentNumberService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class InvoiceController extends Controller
{
    public function index(
        Request $request
    ): Response {
        Gate::authorize(
            'viewAny',
            Invoice::class
        );

        $invoices =
            Invoice::query()
                ->with([
                    'client',
                    'project',
                ])
                ->when(
                    $request->filled('status'),
                    fn ($query) => $query->where(
                        'status',
                        (string) $request->string(
                            'status'
                        )
                    )
                )
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
                                        'invoice_number',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhereHas(
                                        'client',
                                        fn ($client) => $client->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        )
                                    );
                            }
                        );
                    }
                )
                ->latest('invoice_date')
                ->paginate(25)
                ->withQueryString();

        return Inertia::render(
            'admin/invoices/index',
            [
                'invoices' => $invoices,

                'filters' => $request->only([
                    'search',
                    'status',
                ]),
            ]
        );
    }

    public function create(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'invoices.create'
            ),
            403
        );

        $projects =
            Project::query()
                ->whereIn(
                    'status',
                    [
                        'active',
                        'on_hold',
                        'completed',
                    ]
                )
                ->with([
                    'client.contacts',
                ])
                ->latest()
                ->get();

        return Inertia::render(
            'admin/invoices/create',
            [
                'projects' => $projects,
            ]
        );
    }

    public function store(
        StoreInvoiceRequest $request,
        DocumentNumberService $numbers
    ): RedirectResponse {
        $data =
            $request->validated();

        $project =
            Project::with('client')
                ->findOrFail(
                    $data['project_id']
                );

        if (
            ! empty(
                $data[
                    'billing_contact_id'
                ]
            )
        ) {
            $contact =
                ClientContact::findOrFail(
                    $data[
                        'billing_contact_id'
                    ]
                );

            abort_unless(
                $contact->client_id
                === $project->client_id,
                422,
                'Billing contact does not belong to the project client.'
            );
        }

        $invoice =
            Invoice::create([
                ...$data,

                'invoice_number' => $numbers->next(
                    'invoice'
                ),

                'client_id' => $project->client_id,

                'currency' => strtoupper(
                    $data['currency']
                ),

                'status' => 'draft',

                'created_by' => $request->user()->id,
            ]);

        return redirect()
            ->route(
                'admin.invoices.show',
                $invoice
            )
            ->with(
                'success',
                'Invoice created.'
            );
    }

    public function show(
        Invoice $invoice
    ): Response {
        Gate::authorize(
            'view',
            $invoice
        );

        $invoice->load([
            'client',
            'project',
            'billingContact',
            'lines',
            'paymentAllocations.payment',
        ]);

        $billableExpenses =
            collect();

        if (
            $invoice->project_id
            && $invoice->status
                === 'draft'
        ) {
            $billableExpenses =
                ProjectExpense::query()
                    ->where(
                        'project_id',
                        $invoice->project_id
                    )
                    ->where(
                        'status',
                        'approved'
                    )
                    ->where(
                        'billable_to_client',
                        true
                    )
                    ->whereDoesntHave(
                        'project'
                    );

            $billableExpenses =
                ProjectExpense::query()
                    ->where(
                        'project_id',
                        $invoice->project_id
                    )
                    ->where(
                        'status',
                        'approved'
                    )
                    ->where(
                        'billable_to_client',
                        true
                    )
                    ->whereNotIn(
                        'id',
                        InvoiceLine::query()
                            ->where(
                                'source_type',
                                'project_expense'
                            )
                            ->whereNotNull(
                                'source_id'
                            )
                            ->pluck(
                                'source_id'
                            )
                    )
                    ->orderBy(
                        'expense_date'
                    )
                    ->get();
        }

        return Inertia::render(
            'admin/invoices/show',
            [
                'invoice' => $invoice,

                'billableExpenses' => $billableExpenses,
            ]
        );
    }

    public function update(
        UpdateInvoiceRequest $request,
        Invoice $invoice
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $invoice
        );

        $data =
            $request->validated();

        abort_unless(
            (int) $data['project_id']
            === $invoice->project_id,
            422,
            'Invoice project cannot be changed.'
        );

        $invoice->update([
            ...$data,

            'currency' => strtoupper(
                $data['currency']
            ),
        ]);

        return back()->with(
            'success',
            'Invoice updated.'
        );
    }

    public function issue(
        Request $request,
        Invoice $invoice,
        InvoiceIssueService $service
    ): RedirectResponse {
        Gate::authorize(
            'issue',
            $invoice
        );

        $service->issue(
            $invoice,
            $request->user()
        );

        return back()->with(
            'success',
            'Invoice issued.'
        );
    }

    public function send(
        Request $request,
        Invoice $invoice,
        InvoiceIssueService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'invoices.send'
            ),
            403
        );

        $service->send(
            $invoice
        );

        return back()->with(
            'success',
            'Invoice email queued.'
        );
    }

    public function importExpense(
        Invoice $invoice,
        ProjectExpense $expense,
        BillableExpenseImportService $service
    ): RedirectResponse {
        abort_unless(
            request()->user()->can(
                'invoices.update'
            ),
            403
        );

        $service->import(
            $invoice,
            $expense
        );

        return back()->with(
            'success',
            'Billable expense imported.'
        );
    }

    public function destroy(
        Invoice $invoice
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $invoice
        );

        $invoice->delete();

        return redirect()
            ->route(
                'admin.invoices.index'
            )
            ->with(
                'success',
                'Draft invoice deleted.'
            );
    }

    public function pdf(
        Request $request,
        Invoice $invoice
    ): HttpResponse {
        Gate::authorize(
            'view',
            $invoice
        );

        $invoice->load([
            'client',
            'project',
            'lines',
        ]);

        return Pdf::loadView(
            'pdf.invoice',
            [
                'invoice' => $invoice,

                'company' => CompanySetting::first(),
            ]
        )->download(
            "{$invoice->invoice_number}.pdf"
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'invoices.export'
            ),
            403
        );

        return Excel::download(
            new InvoicesExport,
            'invoices.xlsx'
        );
    }
}
