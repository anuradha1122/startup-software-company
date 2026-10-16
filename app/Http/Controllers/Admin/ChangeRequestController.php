<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ChangeRequestsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewChangeRequestRequest;
use App\Http\Requests\Admin\StoreChangeRequestRequest;
use App\Models\ChangeRequest;
use App\Models\Project;
use App\Services\ChangeRequests\ChangeRequestReviewService;
use App\Services\ChangeRequests\ChangeRequestService;
use App\Services\ChangeRequests\ChangeRequestTaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ChangeRequestController extends Controller
{
    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'change-requests.view'
            ),
            403
        );

        return Inertia::render(
            'admin/change-requests/index',
            [
                'changeRequests' => ChangeRequest::query()
                    ->with([
                        'client',
                        'project',
                        'estimate',
                    ])
                    ->latest()
                    ->paginate(25),

                'projects' => Project::query()
                    ->with('client')
                    ->whereIn(
                        'status',
                        [
                            'active',
                            'on_hold',
                            'completed',
                        ]
                    )
                    ->orderBy(
                        'project_number'
                    )
                    ->get(),
            ]
        );
    }

    public function store(
        StoreChangeRequestRequest $request,
        ChangeRequestService $service
    ): RedirectResponse {
        $project =
            Project::findOrFail(
                $request->integer(
                    'project_id'
                )
            );

        $changeRequest =
            $service->createInternal(
                $project,
                $request->validated(),
                $request->user()
            );

        return redirect()
            ->route(
                'admin.change-requests.show',
                $changeRequest
            );
    }

    public function show(
        Request $request,
        ChangeRequest $changeRequest
    ): Response {
        abort_unless(
            $request->user()->can(
                'change-requests.view'
            ),
            403
        );

        return Inertia::render(
            'admin/change-requests/show',
            [
                'changeRequest' => $changeRequest->load([
                    'client',
                    'project',
                    'portalUser',
                    'reviewer',
                    'estimate',
                ]),
            ]
        );
    }

    public function review(
        ReviewChangeRequestRequest $request,
        ChangeRequest $changeRequest,
        ChangeRequestReviewService $service
    ): RedirectResponse {
        $service->decide(
            $changeRequest,
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
            'Change request reviewed.'
        );
    }

    public function convertTask(
        Request $request,
        ChangeRequest $changeRequest,
        ChangeRequestTaskService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'change-requests.convert-task'
            ),
            403
        );

        $task =
            $service->createTask(
                $changeRequest,
                $request->user()
            );

        return redirect()
            ->route(
                'admin.tasks.show',
                $task
            )
            ->with(
                'success',
                'Delivery task created from change request.'
            );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'change-requests.export'
            ),
            403
        );

        return Excel::download(
            new ChangeRequestsExport,
            'change-requests.xlsx'
        );
    }
}
