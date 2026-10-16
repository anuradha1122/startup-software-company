<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreChangeRequestRequest;
use App\Models\ChangeRequest;
use App\Models\Project;
use App\Services\ChangeRequests\ChangeRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ChangeRequestController extends Controller
{
    public function index(): Response
    {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $changeRequests =
            ChangeRequest::query()
                ->where(
                    'client_id',
                    $portalUser->client_id
                )
                ->with([
                    'project',
                    'estimate',
                ])
                ->latest()
                ->get()
                ->map(
                    fn (
                        ChangeRequest $request
                    ) => [
                        'public_id' => $request->public_id,

                        'change_request_number' => $request
                            ->change_request_number,

                        'title' => $request->title,

                        'priority' => $request->priority,

                        'status' => $request->status,

                        'submitted_at' => $request->submitted_at,

                        'project' => [
                            'project_number' => $request
                                ->project
                                ->project_number,

                            'name' => $request
                                ->project
                                ->name,
                        ],

                        'estimate' => $request->estimate
                                ? [
                                    'currency' => $request
                                        ->estimate
                                        ->currency,

                                    'client_amount' => $request
                                        ->estimate
                                        ->client_amount,

                                    'estimated_days' => $request
                                        ->estimate
                                        ->estimated_days,

                                    'client_notes' => $request
                                        ->estimate
                                        ->client_notes,
                                ]
                                : null,
                    ]
                );

        $projects =
            Project::query()
                ->where(
                    'client_id',
                    $portalUser->client_id
                )
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
                ->get([
                    'id',
                    'public_id',
                    'project_number',
                    'name',
                ]);

        return Inertia::render(
            'portal/change-requests/index',
            [
                'changeRequests' => $changeRequests,

                'projects' => $projects,
            ]
        );
    }

    public function store(
        StoreChangeRequestRequest $request,
        Project $project,
        ChangeRequestService $service
    ): RedirectResponse {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $changeRequest =
            $service->createFromPortal(
                $project,
                $request->validated(),
                $portalUser
            );

        return redirect()
            ->route(
                'portal.change-requests.show',
                $changeRequest
            );
    }

    public function show(
        ChangeRequest $changeRequest
    ): Response {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        abort_unless(
            $changeRequest->client_id
            === $portalUser->client_id,
            404
        );

        $changeRequest->load([
            'project',
            'estimate',
        ]);

        return Inertia::render(
            'portal/change-requests/show',
            [
                'changeRequest' => [
                    'public_id' => $changeRequest
                        ->public_id,

                    'change_request_number' => $changeRequest
                        ->change_request_number,

                    'title' => $changeRequest->title,

                    'description' => $changeRequest
                        ->description,

                    'business_reason' => $changeRequest
                        ->business_reason,

                    'impact_summary' => $changeRequest
                        ->impact_summary,

                    'priority' => $changeRequest
                        ->priority,

                    'status' => $changeRequest
                        ->status,

                    'submitted_at' => $changeRequest
                        ->submitted_at,

                    'review_notes' => $changeRequest
                        ->status
                        === 'rejected'
                            ? $changeRequest
                                ->review_notes
                            : null,

                    'project' => [
                        'public_id' => $changeRequest
                            ->project
                            ->public_id,

                        'project_number' => $changeRequest
                            ->project
                            ->project_number,

                        'name' => $changeRequest
                            ->project
                            ->name,
                    ],

                    'estimate' => $changeRequest->estimate
                            ? [
                                'currency' => $changeRequest
                                    ->estimate
                                    ->currency,

                                'client_amount' => $changeRequest
                                    ->estimate
                                    ->client_amount,

                                'estimated_days' => $changeRequest
                                    ->estimate
                                    ->estimated_days,

                                'client_notes' => $changeRequest
                                    ->estimate
                                    ->client_notes,
                            ]
                            : null,
                ],
            ]
        );
    }
}
