<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PayrollRegisterExport;
use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Services\DocumentNumberService;
use App\Services\Payroll\PayrollCalculationService;
use App\Services\Payroll\PayrollWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PayrollPeriodController extends Controller
{
    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'payroll.view'
            ),
            403
        );

        return Inertia::render(
            'admin/hr/payroll/index',
            [
                'periods' => PayrollPeriod::query()
                    ->latest(
                        'period_start'
                    )
                    ->paginate(24),
            ]
        );
    }

    public function store(
        Request $request,
        DocumentNumberService $numbers
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'payroll.calculate'
            ),
            403
        );

        $data =
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'period_start' => [
                    'required',
                    'date',
                ],

                'period_end' => [
                    'required',
                    'date',
                    'after_or_equal:period_start',
                ],

                'payment_date' => [
                    'required',
                    'date',
                    'after_or_equal:period_end',
                ],
            ]);

        PayrollPeriod::create([
            ...$data,

            'payroll_number' => $numbers->next(
                'payroll'
            ),

            'status' => 'draft',
        ]);

        return back()->with(
            'success',
            'Payroll period created.'
        );
    }

    public function calculate(
        Request $request,
        PayrollPeriod $period,
        PayrollCalculationService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'payroll.calculate'
            ),
            403
        );

        $service->calculatePeriod(
            $period
        );

        return back()->with(
            'success',
            'Payroll calculated.'
        );
    }

    public function approve(
        Request $request,
        PayrollPeriod $period,
        PayrollWorkflowService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'payroll.approve'
            ),
            403
        );

        $service->approve(
            $period,
            $request->user()
        );

        return back()->with(
            'success',
            'Payroll approved.'
        );
    }

    public function publish(
        Request $request,
        PayrollPeriod $period,
        PayrollWorkflowService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'payroll.publish'
            ),
            403
        );

        $service->publish(
            $period,
            $request->user()
        );

        return back()->with(
            'success',
            'Payroll published.'
        );
    }

    public function show(
        Request $request,
        PayrollPeriod $period
    ): Response {
        abort_unless(
            $request->user()->can(
                'payroll.view'
            ),
            403
        );

        $period->load([
            'entries.employee.user',
            'entries.adjustments',
        ]);

        return Inertia::render(
            'admin/hr/payroll/show',
            [
                'period' => $period,
            ]
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'payroll.export'
            ),
            403
        );

        return Excel::download(
            new PayrollRegisterExport,
            'payroll-register.xlsx'
        );
    }
}
