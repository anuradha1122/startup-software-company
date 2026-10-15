<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FinancePortfolioExport;
use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Services\Finance\FinancePortfolioService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class FinanceDashboardController extends Controller
{
    public function index(
        Request $request,
        FinancePortfolioService $service
    ): Response {
        abort_unless(
            $request->user()->can(
                'finance.dashboard.view'
            ),
            403
        );

        $projects =
            $service->projects();

        return Inertia::render(
            'admin/finance/dashboard',
            [
                'projects' => $projects
                    ->values(),

                'byCurrency' => $service
                    ->byCurrency(
                        $projects
                    ),
            ]
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'finance.dashboard.export'
            ),
            403
        );

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->event('export')
            ->log(
                'Finance portfolio Excel generated'
            );

        return Excel::download(
            new FinancePortfolioExport,
            'finance-portfolio.xlsx'
        );
    }

    public function pdf(
        Request $request,
        FinancePortfolioService $service
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'finance.dashboard.export'
            ),
            403
        );

        $projects =
            $service->projects();

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->event('export')
            ->log(
                'Finance portfolio PDF generated'
            );

        return Pdf::loadView(
            'pdf.finance-portfolio',
            [
                'projects' => $projects,

                'byCurrency' => $service
                    ->byCurrency(
                        $projects
                    ),

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            'finance-portfolio.pdf'
        );
    }
}
