<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ClientApprovalRequest;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $projects =
            Project::query()
                ->where(
                    'client_id',
                    $portalUser->client_id
                )
                ->whereNotIn(
                    'status',
                    [
                        'cancelled',
                    ]
                )
                ->orderBy(
                    'project_number'
                )
                ->get()
                ->map(
                    fn (Project $project) => [
                        'public_id' => $project->public_id,

                        'project_number' => $project->project_number,

                        'name' => $project->name,

                        'status' => $project->status,
                    ]
                );

        return Inertia::render(
            'portal/dashboard',
            [
                'portalUser' => [
                    'name' => $portalUser->name,

                    'email' => $portalUser->email,

                    'client_name' => $portalUser
                        ->client
                        ->name,
                ],

                'projects' => $projects,

                'pendingApprovalCount' => ClientApprovalRequest::query()
                    ->where(
                        'client_id',
                        $portalUser->client_id
                    )
                    ->where(
                        'status',
                        'pending'
                    )
                    ->count(),

                'outstandingInvoiceCount' => Invoice::query()
                    ->where(
                        'client_id',
                        $portalUser->client_id
                    )
                    ->whereIn(
                        'status',
                        [
                            'issued',
                            'partially_paid',
                            'overdue',
                        ]
                    )
                    ->where(
                        'balance_due',
                        '>',
                        0
                    )
                    ->count(),
            ]
        );
    }
}
