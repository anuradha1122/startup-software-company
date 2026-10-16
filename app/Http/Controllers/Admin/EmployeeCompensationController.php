<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateEmployeeCompensationRequest;
use App\Models\EmployeeProfile;
use App\Services\Hr\EmployeeCompensationService;
use Illuminate\Http\RedirectResponse;

class EmployeeCompensationController extends Controller
{
    public function store(
        UpdateEmployeeCompensationRequest $request,
        EmployeeProfile $employee,
        EmployeeCompensationService $service
    ): RedirectResponse {
        $service->replaceActive(
            $employee,
            $request->validated(),
            $request->user()
        );

        return back()->with(
            'success',
            'Employee compensation updated.'
        );
    }
}
