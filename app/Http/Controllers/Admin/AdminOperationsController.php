<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Operations\OperationalHealthAlertService;
use App\Services\Operations\OperationalHealthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminOperationsController extends Controller
{
    public function index(
        Request $request,
        OperationalHealthService $health
    ): Response {
        abort_unless(
            $request->user()->can(
                'admin-operations.view'
            ),
            403
        );

        return Inertia::render(
            'admin/operations/index',
            [
                'health' => $health->check(),

                'canRunHealthCheck' => $request->user()->can(
                    'admin-operations.health-check'
                ),
            ]
        );
    }

    public function check(
        Request $request,
        OperationalHealthAlertService $alerts
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'admin-operations.health-check'
            ),
            403
        );

        $result =
            $alerts->run();

        activity(
            'admin-operations'
        )
            ->causedBy(
                $request->user()
            )
            ->event(
                'health-check'
            )
            ->withProperties([
                'status' => $result['status'],

                'issue_count' => $result[
                        'issue_count'
                    ],
            ])
            ->log(
                'Operational health check executed manually'
            );

        return back()->with(
            'success',
            "Operational health check completed with status: {$result['status']}."
        );
    }
}
