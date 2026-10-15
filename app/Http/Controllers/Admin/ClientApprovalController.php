<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClientApprovalsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientApprovalRequest;
use App\Models\ClientApprovalRequest;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Services\Portal\ClientApprovalService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ClientApprovalController extends Controller
{
    public function index(
        Request $request,
        Project $project
    ): Response {
        abort_unless(
            $request->user()->can(
                'client-approvals.view'
            ),
            403
        );

        return Inertia::render(
            'admin/projects/client-approvals/index',
            [
                'project' => [
                    'public_id' => $project->public_id,

                    'project_number' => $project->project_number,

                    'name' => $project->name,
                ],

                'approvals' => $project
                    ->clientApprovalRequests()
                    ->with([
                        'requester',
                        'responder',
                    ])
                    ->latest(
                        'requested_at'
                    )
                    ->get(),
            ]
        );
    }

    public function store(
        StoreClientApprovalRequest $request,
        Project $project,
        ClientApprovalService $service
    ): RedirectResponse {
        $service->request(
            $project,
            $request->validated(),
            $request->user()
        );

        return back()->with(
            'success',
            'Client approval request sent.'
        );
    }

    public function cancel(
        Request $request,
        Project $project,
        ClientApprovalRequest $approval,
        ClientApprovalService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'client-approvals.cancel'
            ),
            403
        );

        abort_unless(
            $approval->project_id
            === $project->id,
            404
        );

        $service->cancel(
            $approval
        );

        return back()->with(
            'success',
            'Approval request cancelled.'
        );
    }

    public function excel(
        Request $request,
        Project $project
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'client-approvals.export'
            ),
            403
        );

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $project
            )
            ->event('export')
            ->log(
                'Client approval Excel generated'
            );

        return Excel::download(
            new ClientApprovalsExport(
                $project
            ),
            "{$project->project_number}-client-approvals.xlsx"
        );
    }

    public function pdf(
        Request $request,
        Project $project
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'client-approvals.export'
            ),
            403
        );

        $approvals =
            $project
                ->clientApprovalRequests()
                ->with([
                    'responder',
                ])
                ->orderBy(
                    'requested_at'
                )
                ->get();

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $project
            )
            ->event('export')
            ->log(
                'Client approval PDF generated'
            );

        return Pdf::loadView(
            'pdf.client-approvals',
            [
                'project' => $project->load(
                    'client'
                ),

                'approvals' => $approvals,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            "{$project->project_number}-client-approvals.pdf"
        );
    }
}
