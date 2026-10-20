<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewProcurementRequestRequest;
use App\Models\ProcurementRequest;
use App\Services\Procurement\ProcurementReviewService;
use Illuminate\Http\RedirectResponse;

class ProcurementReviewController extends Controller
{
    public function store(
        ReviewProcurementRequestRequest $request,
        ProcurementRequest $procurementRequest,
        ProcurementReviewService $service
    ): RedirectResponse {
        $validated =
            $request->validated();

        $service->review(
            $procurementRequest,
            $request->user(),
            $validated['decision'],
            $validated['review_notes']
            ?? null
        );

        return back();
    }
}
