<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectInternalCostExport;
use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Models\TimesheetEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ProjectInternalCostController extends Controller
{
    public function show(
        Request $request,
        Project $project
    ): Response {
        abort_unless(
            $request->user()->can(
                'internal-costs.view'
            ),
            403
        );

        $entries =
            $this->approvedEntries(
                $project
            );

        return Inertia::render(
            'admin/projects/internal-costs/index',
            [
                'project' => $project->load(
                    'client'
                ),

                'entries' => $entries,

                'summary' => [
                'hours' => round(
                    (float) $entries
                        ->sum('hours'),
                    2
                ),

                'internal_cost' => round(
                    (float) $entries
                        ->sum(
                            'internal_cost_amount'
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
                'internal-costs.export'
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
                'Project internal cost Excel generated'
            );

        return Excel::download(
            new ProjectInternalCostExport(
                $project
            ),
            "{$project->project_number}-internal-cost.xlsx"
        );
    }

    public function pdf(
        Request $request,
        Project $project
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'internal-costs.export'
            ),
            403
        );

        $entries =
            $this->approvedEntries(
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
                'Project internal cost PDF generated'
            );

        return Pdf::loadView(
            'pdf.project-internal-cost',
            [
                'project' => $project->load(
                    'client'
                ),

                'entries' => $entries,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            "{$project->project_number}-internal-cost.pdf"
        );
    }

    private function approvedEntries(
        Project $project
    ) {
        return TimesheetEntry::query()
            ->where(
                'project_id',
                $project->id
            )
            ->whereHas(
                'timesheet',
                fn ($query) => $query->where(
                    'status',
                    'approved'
                )
            )
            ->with([
                'timesheet.user',
                'task',
                'assignment',
            ])
            ->orderBy(
                'work_date'
            )
            ->get();
    }
}
