<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectProfitabilityExport;
use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Models\ProjectFinanceSetting;
use App\Services\Finance\ProjectProfitabilityService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ProjectProfitabilityController extends Controller
{
    public function show(
        Request $request,
        Project $project,
        ProjectProfitabilityService $service
    ): Response {
        abort_unless(
            $request->user()->can(
                'project-profitability.view'
            ),
            403
        );

        $financials =
            $service
                ->calculate(
                    $project
                );

        $setting =
            ProjectFinanceSetting::firstOrCreate(
                [
                    'project_id' => $project->id,
                ],
                [
                    'target_margin_percent' => 30,

                    'warning_margin_percent' => 20,

                    'alerts_enabled' => true,
                ]
            );

        return Inertia::render(
            'admin/projects/profitability/show',
            [
                'project' => $project->load(
                    'client'
                ),

                'financials' => $financials->toArray(),

                'financeSetting' => $setting,

                'snapshots' => $project
                    ->financialSnapshots()
                    ->latest(
                        'snapshot_date'
                    )
                    ->limit(30)
                    ->get(),

                'canManageSettings' => $request->user()->can(
                    'project-finance-settings.manage'
                ),

                'canSnapshot' => $request->user()->can(
                    'project-profitability.snapshot'
                ),

                'canExport' => $request->user()->can(
                    'project-profitability.export'
                ),
            ]
        );
    }

    public function excel(
        Request $request,
        Project $project
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'project-profitability.export'
            ),
            403
        );

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $project
            )
            ->event('export')
            ->log(
                'Project profitability Excel generated'
            );

        return Excel::download(
            new ProjectProfitabilityExport(
                $project
            ),
            "{$project->project_number}-profitability.xlsx"
        );
    }

    public function pdf(
        Request $request,
        Project $project,
        ProjectProfitabilityService $service
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'project-profitability.export'
            ),
            403
        );

        $financials =
            $service
                ->calculate(
                    $project
                );

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $project
            )
            ->event('export')
            ->log(
                'Project profitability PDF generated'
            );

        return Pdf::loadView(
            'pdf.project-profitability',
            [
                'project' => $project->load(
                    'client'
                ),

                'financials' => $financials,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            "{$project->project_number}-profitability.pdf"
        );
    }
}
