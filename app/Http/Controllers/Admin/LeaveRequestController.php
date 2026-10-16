<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LeaveRequestsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewLeaveRequestRequest;
use App\Models\LeaveRequest;
use App\Services\Leave\LeaveRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LeaveRequestController extends Controller
{
    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'leave-requests.view-all'
            ),
            403
        );

        return Inertia::render(
            'admin/hr/leave/index',
            [
                'requests' => LeaveRequest::query()
                    ->with([
                        'employee.user',
                        'leaveType',
                        'reviewer',
                    ])
                    ->latest()
                    ->paginate(30),
            ]
        );
    }

    public function review(
        ReviewLeaveRequestRequest $request,
        LeaveRequest $leaveRequest,
        LeaveRequestService $service
    ): RedirectResponse {
        $service->review(
            $leaveRequest,
            $request->user(),
            $request->validated(
                'decision'
            ),
            $request->validated(
                'review_notes'
            )
        );

        return back()->with(
            'success',
            'Leave request reviewed.'
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'leave-requests.export'
            ),
            403
        );

        return Excel::download(
            new LeaveRequestsExport,
            'leave-requests.xlsx'
        );
    }
}
