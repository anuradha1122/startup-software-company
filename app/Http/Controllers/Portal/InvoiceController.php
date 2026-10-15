<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class InvoiceController extends Controller
{
    public function index(): Response
    {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $invoices =
            Invoice::query()
                ->where(
                    'client_id',
                    $portalUser->client_id
                )
                ->whereIn(
                    'status',
                    [
                        'issued',
                        'partially_paid',
                        'paid',
                        'overdue',
                    ]
                )
                ->with('project')
                ->latest(
                    'invoice_date'
                )
                ->get()
                ->map(
                    fn (Invoice $invoice) => [
                        'public_id' => $invoice->public_id,

                        'invoice_number' => $invoice->invoice_number,

                        'invoice_date' => $invoice->invoice_date,

                        'due_date' => $invoice->due_date,

                        'currency' => $invoice->currency,

                        'total_amount' => $invoice->total_amount,

                        'amount_paid' => $invoice->amount_paid,

                        'balance_due' => $invoice->balance_due,

                        'status' => $invoice->status,

                        'project' => $invoice->project
                                ? [
                                    'project_number' => $invoice
                                        ->project
                                        ->project_number,

                                    'name' => $invoice
                                        ->project
                                        ->name,
                                ]
                                : null,
                    ]
                );

        return Inertia::render(
            'portal/invoices/index',
            [
                'invoices' => $invoices,
            ]
        );
    }

    public function pdf(
        Invoice $invoice
    ): HttpResponse {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        abort_unless(
            $invoice->client_id
            === $portalUser->client_id,
            404
        );

        abort_unless(
            in_array(
                $invoice->status,
                [
                    'issued',
                    'partially_paid',
                    'paid',
                    'overdue',
                ],
                true
            ),
            404
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
}
