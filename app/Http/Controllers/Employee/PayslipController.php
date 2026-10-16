<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\PayrollEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PayslipController extends Controller
{
    public function index(
        Request $request
    ): Response {
        $employee =
            $request
                ->user()
                ->employeeProfile;

        abort_unless(
            $employee,
            404
        );

        $entries =
            PayrollEntry::query()
                ->where(
                    'employee_profile_id',
                    $employee->id
                )
                ->whereHas(
                    'period',
                    fn ($query) => $query->whereIn(
                        'status',
                        [
                            'published',
                            'closed',
                        ]
                    )
                )
                ->with('period')
                ->latest('id')
                ->get()
                ->map(
                    fn (PayrollEntry $entry) => [
                        'public_id' => $entry->public_id,

                        'currency' => $entry->currency,

                        'gross_pay' => $entry->gross_pay,

                        'deductions' => $entry->deductions,

                        'net_pay' => $entry->net_pay,

                        'period' => [
                            'name' => $entry
                                ->period
                                ->name,

                            'payment_date' => $entry
                                ->period
                                ->payment_date,
                        ],
                    ]
                );

        return Inertia::render(
            'employee/payslips/index',
            [
                'payslips' => $entries,
            ]
        );
    }

    public function pdf(
        Request $request,
        PayrollEntry $entry
    ): HttpResponse {
        $employee =
            $request
                ->user()
                ->employeeProfile;

        abort_unless(
            $employee
            && $entry
                ->employee_profile_id
                === $employee->id,
            404
        );

        abort_unless(
            in_array(
                $entry
                    ->period
                    ->status,
                [
                    'published',
                    'closed',
                ],
                true
            ),
            404
        );

        $entry->load([
            'employee.user',
            'period',
            'adjustments',
        ]);

        return Pdf::loadView(
            'pdf.payslip',
            [
                'entry' => $entry,

                'company' => CompanySetting::first(),
            ]
        )->download(
            "payslip-{$entry->period->payment_date->format('Y-m')}.pdf"
        );
    }
}
