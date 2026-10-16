<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmployeesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEmployeeRequest;
use App\Models\EmployeeProfile;
use App\Models\User;
use App\Services\Hr\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeController extends Controller
{
    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'employees.view'
            ),
            403
        );

        return Inertia::render(
            'admin/hr/employees/index',
            [
                'employees' => EmployeeProfile::query()
                    ->with('user')
                    ->latest()
                    ->paginate(25),

                'availableUsers' => User::query()
                    ->whereDoesntHave(
                        'employeeProfile'
                    )
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderBy('name')
                    ->get([
                        'id',
                        'public_id',
                        'name',
                        'email',
                    ]),
            ]
        );
    }

    public function store(
        StoreEmployeeRequest $request,
        EmployeeService $service
    ): RedirectResponse {
        $employee =
            $service->create(
                $request->validated(),
                $request->user()
            );

        return redirect()
            ->route(
                'admin.hr.employees.show',
                $employee
            )
            ->with(
                'success',
                'Employee profile created.'
            );
    }

    public function show(
        Request $request,
        EmployeeProfile $employee
    ): Response {
        abort_unless(
            $request->user()->can(
                'employees.view'
            ),
            403
        );

        $employee->load([
            'user',
            'leaveBalances.leaveType',
        ]);

        $payload = [
            'public_id' => $employee->public_id,

            'employee_number' => $employee->employee_number,

            'employment_type' => $employee->employment_type,

            'status' => $employee->status,

            'department' => $employee->department,

            'job_title' => $employee->job_title,

            'hire_date' => $employee->hire_date,

            'work_location' => $employee->work_location,

            'user' => [
                'public_id' => $employee->user->public_id,

                'name' => $employee->user->name,

                'email' => $employee->user->email,
            ],

            'leave_balances' => $employee
                ->leaveBalances
                ->map(
                    fn ($balance) => [
                        'year' => $balance->year,

                        'available' => $balance->available,

                        'leave_type' => [
                            'name' => $balance
                                ->leaveType
                                ->name,
                        ],
                    ]
                ),
        ];

        if (
            $request->user()->can(
                'employee-compensation.view'
            )
        ) {
            $compensation =
                $employee
                    ->activeCompensation()
                    ->first();

            $payload[
                'compensation'
            ] =
                $compensation
                    ? [
                        'currency' => $compensation
                            ->currency,

                        'monthly_basic_salary' => $compensation
                            ->monthly_basic_salary,

                        'fixed_allowance' => $compensation
                            ->fixed_allowance,

                        'fixed_deduction' => $compensation
                            ->fixed_deduction,

                        'effective_from' => $compensation
                            ->effective_from,
                    ]
                    : null;
        }

        return Inertia::render(
            'admin/hr/employees/show',
            [
                'employee' => $payload,

                'canViewCompensation' => $request->user()->can(
                    'employee-compensation.view'
                ),

                'canManageCompensation' => $request->user()->can(
                    'employee-compensation.manage'
                ),
            ]
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'employees.export'
            ),
            403
        );

        return Excel::download(
            new EmployeesExport,
            'employees.xlsx'
        );
    }
}
