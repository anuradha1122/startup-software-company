<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreLeaveRequestRequest;
use App\Models\LeaveType;
use App\Services\Leave\LeaveRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaveController extends Controller
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

        return Inertia::render(
            'employee/leave/index',
            [
                'leaveTypes' => LeaveType::query()
                    ->where(
                        'active',
                        true
                    )
                    ->orderBy(
                        'sort_order'
                    )
                    ->get([
                        'id',
                        'public_id',
                        'code',
                        'name',
                        'paid',
                    ]),

                'balances' => $employee
                    ->leaveBalances()
                    ->with(
                        'leaveType'
                    )
                    ->where(
                        'year',
                        today()->year
                    )
                    ->get(),

                'requests' => $employee
                    ->leaveRequests()
                    ->with(
                        'leaveType'
                    )
                    ->latest()
                    ->get(),
            ]
        );
    }

    public function store(
        StoreLeaveRequestRequest $request,
        LeaveRequestService $service
    ): RedirectResponse {
        $employee =
            $request
                ->user()
                ->employeeProfile;

        abort_unless(
            $employee,
            404
        );

        $service->submit(
            $employee,
            $request->validated()
        );

        return back()->with(
            'success',
            'Leave request submitted.'
        );
    }
}
