<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\Attendance\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
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
            'employee/attendance/index',
            [
                'today' => $employee
                    ->attendanceRecords()
                    ->whereDate(
                        'work_date',
                        today()
                    )
                    ->first(),

                'records' => $employee
                    ->attendanceRecords()
                    ->latest(
                        'work_date'
                    )
                    ->limit(31)
                    ->get(),
            ]
        );
    }

    public function clockIn(
        Request $request,
        AttendanceService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'attendance.clock'
            ),
            403
        );

        $service->clockIn(
            $request
                ->user()
                ->employeeProfile
        );

        return back()->with(
            'success',
            'Clock-in recorded.'
        );
    }

    public function clockOut(
        Request $request,
        AttendanceService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'attendance.clock'
            ),
            403
        );

        $service->clockOut(
            $request
                ->user()
                ->employeeProfile
        );

        return back()->with(
            'success',
            'Clock-out recorded.'
        );
    }
}
