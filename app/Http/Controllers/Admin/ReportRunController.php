<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportRunsExport;
use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\ReportRun;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ReportRunController extends Controller
{
    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'report-runs.view'
            ),
            403
        );

        $runs =
            ReportRun::query()
                ->with('generator')
                ->when(
                    $request->filled(
                        'report_type'
                    ),
                    fn ($query) => $query->where(
                        'report_type',
                        (string) $request
                            ->string(
                                'report_type'
                            )
                    )
                )
                ->when(
                    $request->filled(
                        'status'
                    ),
                    fn ($query) => $query->where(
                        'status',
                        (string) $request
                            ->string(
                                'status'
                            )
                    )
                )
                ->latest(
                    'generated_at'
                )
                ->paginate(50)
                ->withQueryString();

        return Inertia::render(
            'admin/reports/runs',
            [
                'runs' => $runs,

                'filters' => $request->only([
                    'report_type',
                    'status',
                ]),
            ]
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'report-runs.export'
            ),
            403
        );

        return Excel::download(
            new ReportRunsExport,
            'report-runs.xlsx'
        );
    }

    public function pdf(
        Request $request
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'report-runs.export'
            ),
            403
        );

        $runs =
            ReportRun::query()
                ->with('generator')
                ->latest(
                    'generated_at'
                )
                ->get();

        return Pdf::loadView(
            'pdf.report-runs',
            [
                'runs' => $runs,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            'report-runs.pdf'
        );
    }
}
