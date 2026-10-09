<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AuditLogsExport;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAccess($request);

        return Inertia::render('admin/audit/index', [
            'activities' => $this->query($request)
                ->with('causer')
                ->paginate(25)
                ->withQueryString(),

            'filters' => $request->only([
                'search',
                'log_name',
                'date_from',
                'date_to',
            ]),
        ]);
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        $this->authorizeAccess($request);

        activity('exports')
            ->causedBy($request->user())
            ->event('export')
            ->log('Audit log exported to Excel');

        return Excel::download(
            new AuditLogsExport(
                $request->only([
                    'search',
                    'log_name',
                    'date_from',
                    'date_to',
                ])
            ),
            'audit-log-'.now()->format('Y-m-d-His').'.xlsx'
        );
    }

    public function pdf(
        Request $request
    ): HttpResponse {
        $this->authorizeAccess($request);

        $activities = $this->query($request)
            ->with('causer')
            ->limit(1000)
            ->get();

        activity('exports')
            ->causedBy($request->user())
            ->event('export')
            ->log('Audit log exported to PDF');

        return Pdf::loadView(
            'pdf.audit-log',
            [
                'activities' => $activities,
                'generatedBy' => $request->user(),
                'generatedAt' => now(),
            ]
        )
            ->setPaper('a4', 'landscape')
            ->download(
                'audit-log-'
                .now()->format('Y-m-d-His')
                .'.pdf'
            );
    }

    private function authorizeAccess(
        Request $request
    ): void {
        abort_unless(
            $request->user()->can('audit.view'),
            403
        );
    }

    private function query(
        Request $request
    ): Builder {
        return Activity::query()
            ->latest()
            ->when(
                $request->filled('search'),
                function (
                    Builder $query
                ) use ($request): void {
                    $search = $request->string('search');

                    $query->where(
                        function (
                            Builder $inner
                        ) use ($search): void {
                            $inner
                                ->where(
                                    'description',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'event',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $request->filled('log_name'),
                fn (Builder $query) => $query->where(
                    'log_name',
                    $request->string('log_name')
                )
            )
            ->when(
                $request->filled('date_from'),
                fn (Builder $query) => $query->whereDate(
                    'created_at',
                    '>=',
                    $request->date('date_from')
                )
            )
            ->when(
                $request->filled('date_to'),
                fn (Builder $query) => $query->whereDate(
                    'created_at',
                    '<=',
                    $request->date('date_to')
                )
            );
    }
}
