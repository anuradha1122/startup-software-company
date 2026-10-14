<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TimesheetExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTimesheetRequest;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Models\Timesheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TimesheetController extends Controller
{
    public function index(
        Request $request
    ): Response {
        Gate::authorize(
            'viewAny',
            Timesheet::class
        );

        $query =
            Timesheet::query()
                ->with('user')
                ->latest(
                    'week_start'
                );

        if (
            ! $request->user()->can(
                'timesheets.view-all'
            )
        ) {
            $query->where(
                'user_id',
                $request->user()->id
            );
        }

        return Inertia::render(
            'admin/timesheets/index',
            [
                'timesheets' => $query
                    ->paginate(20),

                'canViewAll' => $request->user()->can(
                    'timesheets.view-all'
                ),
            ]
        );
    }

    public function store(
        StoreTimesheetRequest $request
    ): RedirectResponse {
        $weekStart =
            Carbon::parse(
                $request->validated(
                    'week_start'
                )
            )->startOfWeek(
                Carbon::MONDAY
            );

        $weekEnd =
            $weekStart
                ->copy()
                ->endOfWeek(
                    Carbon::SUNDAY
                );

        $timesheet =
            Timesheet::query()
                ->where(
                    'user_id',
                    $request->user()->id
                )
                ->whereDate(
                    'week_start',
                    $weekStart->toDateString()
                )
                ->first();

        if (! $timesheet) {
            $timesheet =
                Timesheet::create([
                    'user_id' => $request->user()->id,

                    'week_start' => $weekStart,

                    'week_end' => $weekEnd,

                    'status' => 'draft',

                    'notes' => $request->validated(
                        'notes'
                    ),
                ]);
        }

        return redirect()
            ->route(
                'admin.timesheets.show',
                $timesheet
            );
    }

    public function show(
        Request $request,
        Timesheet $timesheet
    ): Response {
        Gate::authorize(
            'view',
            $timesheet
        );

        $timesheet->load([
            'user.resourceCostRates',
            'reviewer',
            'entries.project',
            'entries.task.workflowStatus',
            'entries.assignment',
        ]);

        $projects =
            Project::query()
                ->whereHas(
                    'assignments',
                    function ($query) use (
                        $timesheet
                    ): void {
                        $query
                            ->where(
                                'user_id',
                                $timesheet
                                    ->user_id
                            )
                            ->whereIn(
                                'status',
                                [
                                    'active',
                                    'ended',
                                ]
                            );
                    }
                )
                ->with([
                    'tasks' => fn ($query) => $query
                        ->with(
                            'workflowStatus'
                        )
                        ->orderBy(
                            'task_sequence'
                        ),
                ])
                ->orderBy(
                    'project_number'
                )
                ->get();

        return Inertia::render(
            'admin/timesheets/show',
            [
                'timesheet' => $timesheet,

                'projects' => $projects,

                'canReview' => $request->user()->can(
                    'timesheets.review'
                ),
            ]
        );
    }

    public function pdf(
        Request $request,
        Timesheet $timesheet
    ): HttpResponse {
        Gate::authorize(
            'view',
            $timesheet
        );

        abort_unless(
            $request->user()->can(
                'timesheets.export'
            ),
            403
        );

        $timesheet->load([
            'user',
            'entries.project',
            'entries.task',
        ]);

        return Pdf::loadView(
            'pdf.timesheet',
            [
                'timesheet' => $timesheet,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            "timesheet-{$timesheet->week_start->format('Y-m-d')}.pdf"
        );
    }

    public function excel(
        Request $request,
        Timesheet $timesheet
    ): BinaryFileResponse {
        Gate::authorize(
            'view',
            $timesheet
        );

        abort_unless(
            $request->user()->can(
                'timesheets.export'
            ),
            403
        );

        return Excel::download(
            new TimesheetExport(
                $timesheet
            ),
            "timesheet-{$timesheet->week_start->format('Y-m-d')}.xlsx"
        );
    }
}
