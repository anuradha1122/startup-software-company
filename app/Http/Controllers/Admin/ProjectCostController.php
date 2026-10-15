<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectNonLaborCostExport;
use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Models\ProjectExpense;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ProjectCostController extends Controller
{
    public function show(
        Request $request,
        Project $project
    ): Response {
        abort_unless(
            $request->user()->can(
                'non-labor-costs.view'
            ),
            403
        );

        $expenses =
            $this->approvedExpenses(
                $project
            );

        return Inertia::render(
            'admin/projects/non-labor-costs/index',
            [
                'project' => $project->load(
                    'client'
                ),

                'expenses' => $expenses,

                'summary' => [
                    'approved_count' => $expenses->count(),

                    'total_non_labor_cost' => round(
                        (float) $expenses
                            ->sum(
                                'total_amount'
                            ),
                        2
                    ),
                ],
            ]
        );
    }

    public function excel(
        Request $request,
        Project $project
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'non-labor-costs.export'
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
                'Project non-labor cost Excel generated'
            );

        return Excel::download(
            new ProjectNonLaborCostExport(
                $project
            ),
            "{$project->project_number}-non-labor-cost.xlsx"
        );
    }

    public function pdf(
        Request $request,
        Project $project
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'non-labor-costs.export'
            ),
            403
        );

        $expenses =
            $this->approvedExpenses(
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
                'Project non-labor cost PDF generated'
            );

        return Pdf::loadView(
            'pdf.project-non-labor-cost',
            [
                'project' => $project->load(
                    'client'
                ),

                'expenses' => $expenses,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            "{$project->project_number}-non-labor-cost.pdf"
        );
    }

    private function approvedExpenses(
        Project $project
    ) {
        return ProjectExpense::query()
            ->where(
                'project_id',
                $project->id
            )
            ->where(
                'status',
                'approved'
            )
            ->with([
                'supplier',
                'incurredBy',
            ])
            ->orderBy(
                'expense_date'
            )
            ->get();
    }
}
