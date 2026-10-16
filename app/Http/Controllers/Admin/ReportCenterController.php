<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportRun;
use App\Services\Reports\ReportCatalogService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportCenterController extends Controller
{
    public function index(
        Request $request,
        ReportCatalogService $catalog
    ): Response {
        abort_unless(
            $request->user()->can(
                'reports.view'
            ),
            403
        );

        return Inertia::render(
            'admin/reports/index',
            [
                'reports' => $catalog->all(),

                'recentRuns' => ReportRun::query()
                    ->with('generator')
                    ->latest(
                        'generated_at'
                    )
                    ->limit(10)
                    ->get()
                    ->map(
                        fn (
                            ReportRun $run
                        ) => [
                            'public_id' => $run->public_id,

                            'report_type' => $run->report_type,

                            'format' => $run->format,

                            'status' => $run->status,

                            'download_name' => $run
                                ->download_name,

                            'generated_at' => $run
                                ->generated_at,

                            'generated_by' => $run
                                ->generator
                                ?->name,
                        ]
                    ),

                'canViewRuns' => $request->user()->can(
                    'report-runs.view'
                ),

                'canExport' => $request->user()->can(
                    'reports.export'
                ),
            ]
        );
    }
}
