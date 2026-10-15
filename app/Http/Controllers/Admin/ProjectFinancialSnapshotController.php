<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\Finance\ProjectFinancialSnapshotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectFinancialSnapshotController extends Controller
{
    public function store(
        Request $request,
        Project $project,
        ProjectFinancialSnapshotService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'project-profitability.snapshot'
            ),
            403
        );

        $service->capture(
            $project,
            $request->user()
        );

        return back()->with(
            'success',
            'Financial snapshot captured.'
        );
    }
}
