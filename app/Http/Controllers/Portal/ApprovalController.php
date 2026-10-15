<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\RespondClientApprovalRequest;
use App\Models\ClientApprovalRequest;
use App\Services\Portal\ClientApprovalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ApprovalController extends Controller
{
    public function index(): Response
    {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $approvals =
            ClientApprovalRequest::query()
                ->where(
                    'client_id',
                    $portalUser->client_id
                )
                ->with('project')
                ->latest(
                    'requested_at'
                )
                ->get()
                ->map(
                    fn (
                        ClientApprovalRequest $approval
                    ) => [
                        'public_id' => $approval->public_id,

                        'approval_number' => $approval->approval_number,

                        'type' => $approval->type,

                        'title' => $approval->title,

                        'status' => $approval->status,

                        'requested_at' => $approval->requested_at,

                        'due_at' => $approval->due_at,

                        'project' => [
                            'project_number' => $approval
                                ->project
                                ->project_number,

                            'name' => $approval
                                ->project
                                ->name,
                        ],
                    ]
                );

        return Inertia::render(
            'portal/approvals/index',
            [
                'approvals' => $approvals,
            ]
        );
    }

    public function show(
        ClientApprovalRequest $approval
    ): Response {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        abort_unless(
            $approval->client_id
            === $portalUser->client_id,
            404
        );

        $approval->load(
            'project'
        );

        return Inertia::render(
            'portal/approvals/show',
            [
                'approval' => [
                    'public_id' => $approval->public_id,

                    'approval_number' => $approval->approval_number,

                    'type' => $approval->type,

                    'title' => $approval->title,

                    'summary' => $approval->summary,

                    'status' => $approval->status,

                    'requested_at' => $approval->requested_at,

                    'due_at' => $approval->due_at,

                    'response_comment' => $approval
                        ->response_comment,

                    'responded_at' => $approval
                        ->responded_at,

                    'project' => [
                        'public_id' => $approval
                            ->project
                            ->public_id,

                        'project_number' => $approval
                            ->project
                            ->project_number,

                        'name' => $approval
                            ->project
                            ->name,
                    ],
                ],
            ]
        );
    }

    public function respond(
        RespondClientApprovalRequest $request,
        ClientApprovalRequest $approval,
        ClientApprovalService $service
    ): RedirectResponse {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $service->respond(
            $approval,
            $portalUser,
            $request->validated(
                'decision'
            ),
            $request->validated(
                'response_comment'
            )
        );

        return back()->with(
            'success',
            'Your response has been recorded.'
        );
    }
}
