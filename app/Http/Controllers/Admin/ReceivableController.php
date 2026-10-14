<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReceivablesExport;
use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ReceivableController extends Controller
{
    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'receivables.view'
            ),
            403
        );

        $invoices =
            Invoice::query()
                ->with([
                    'client',
                    'project',
                ])
                ->whereIn(
                    'status',
                    [
                        'issued',
                        'partially_paid',
                        'overdue',
                    ]
                )
                ->where(
                    'balance_due',
                    '>',
                    0
                )
                ->orderBy(
                    'due_date'
                )
                ->get();

        return Inertia::render(
            'admin/receivables/index',
            [
                'invoices' => $invoices,

                'summary' => [
                    'outstanding' => round(
                        (float) $invoices
                            ->sum(
                                'balance_due'
                            ),
                        2
                    ),

                    'overdue' => round(
                        (float) $invoices
                            ->where(
                                'status',
                                'overdue'
                            )
                            ->sum(
                                'balance_due'
                            ),
                        2
                    ),

                    'count' => $invoices->count(),
                ],
            ]
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'receivables.export'
            ),
            403
        );

        return Excel::download(
            new ReceivablesExport,
            'receivables.xlsx'
        );
    }

    public function pdf(
        Request $request
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'receivables.export'
            ),
            403
        );

        $invoices =
            Invoice::query()
                ->with('client')
                ->whereIn(
                    'status',
                    [
                        'issued',
                        'partially_paid',
                        'overdue',
                    ]
                )
                ->where(
                    'balance_due',
                    '>',
                    0
                )
                ->orderBy(
                    'due_date'
                )
                ->get();

        return Pdf::loadView(
            'pdf.receivables',
            [
                'invoices' => $invoices,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            'receivables.pdf'
        );
    }
}
