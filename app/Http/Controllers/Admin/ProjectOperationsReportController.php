<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectOperationsExport;
use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Services\Reports\ProjectOperationsReportService;
use App\Services\Reports\ReportRunService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Throwable;

class ProjectOperationsReportController extends Controller
{
    public function excel(
        Request $request,
        ProjectOperationsReportService $report,
        ReportRunService $runs
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'reports.export'
            ),
            403
        );

        $rows =
            $report->rows();

        $filename =
            'project-operations.xlsx';

        try {
            $runs->completed(
                type: 'project_operations',
                format: 'excel',
                user: $request->user(),
                downloadName: $filename,
                rowCount: $rows->count()
            );

            activity('exports')
                ->causedBy(
                    $request->user()
                )
                ->event('export')
                ->log(
                    'M13 project operations Excel generated'
                );

            return Excel::download(
                new ProjectOperationsExport(
                    $rows
                ),
                $filename
            );
        } catch (Throwable $exception) {
            $runs->failed(
                type: 'project_operations',
                format: 'excel',
                user: $request->user(),
                exception: $exception
            );

            throw $exception;
        }
    }

    public function pdf(
        Request $request,
        ProjectOperationsReportService $report,
        ReportRunService $runs
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'reports.export'
            ),
            403
        );

        $rows =
            $report->rows();

        $filename =
            'project-operations.pdf';

        try {
            $runs->completed(
                type: 'project_operations',
                format: 'pdf',
                user: $request->user(),
                downloadName: $filename,
                rowCount: $rows->count()
            );

            activity('exports')
                ->causedBy(
                    $request->user()
                )
                ->event('export')
                ->log(
                    'M13 project operations PDF generated'
                );

            return Pdf::loadView(
                'pdf.project-operations',
                [
                    'rows' => $rows,

                    'company' => CompanySetting::first(),

                    'generatedBy' => $request->user(),

                    'generatedAt' => now(),
                ]
            )->download(
                $filename
            );
        } catch (Throwable $exception) {
            $runs->failed(
                type: 'project_operations',
                format: 'pdf',
                user: $request->user(),
                exception: $exception
            );

            throw $exception;
        }
    }
}
