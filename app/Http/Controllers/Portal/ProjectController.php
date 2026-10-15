<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ClientPortalDocument;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ProjectController extends Controller
{
    public function show(
        Project $project
    ): Response {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $this->assertProjectAccess(
            $project,
            $portalUser->client_id
        );

        $project->load([
            'milestones',
            'tasks.workflowStatus',
        ]);

        $milestones =
            $project
                ->milestones
                ->map(
                    fn ($milestone) => [
                        'public_id' => $milestone->public_id,

                        'name' => $milestone->name,

                        'status' => $milestone->status,

                        'due_date' => $milestone
                            ->getAttribute(
                                'due_date'
                            ),
                    ]
                );

        $tasks =
            $project
                ->tasks
                ->where(
                    'client_visible',
                    true
                )
                ->map(
                    fn ($task) => [
                        'public_id' => $task->public_id,

                        'task_number' => $task->task_number,

                        'title' => $task->title,

                        'status' => $task
                            ->workflowStatus
                            ?->name,
                    ]
                )
                ->values();

        $documents =
            ClientPortalDocument::query()
                ->where(
                    'client_id',
                    $portalUser->client_id
                )
                ->where(
                    'project_id',
                    $project->id
                )
                ->where(
                    'published_at',
                    '<=',
                    now()
                )
                ->where(
                    function ($query): void {
                        $query
                            ->whereNull(
                                'expires_at'
                            )
                            ->orWhere(
                                'expires_at',
                                '>',
                                now()
                            );
                    }
                )
                ->latest(
                    'published_at'
                )
                ->get()
                ->map(
                    fn (
                        ClientPortalDocument $document
                    ) => [
                        'public_id' => $document->public_id,

                        'category' => $document->category,

                        'title' => $document->title,

                        'description' => $document->description,

                        'published_at' => $document->published_at,
                    ]
                );

        $invoices =
            Invoice::query()
                ->where(
                    'client_id',
                    $portalUser->client_id
                )
                ->where(
                    'project_id',
                    $project->id
                )
                ->whereIn(
                    'status',
                    [
                        'issued',
                        'partially_paid',
                        'paid',
                        'overdue',
                    ]
                )
                ->orderByDesc(
                    'invoice_date'
                )
                ->get()
                ->map(
                    fn (Invoice $invoice) => [
                        'public_id' => $invoice->public_id,

                        'invoice_number' => $invoice->invoice_number,

                        'invoice_date' => $invoice->invoice_date,

                        'due_date' => $invoice->due_date,

                        'currency' => $invoice->currency,

                        'total_amount' => $invoice->total_amount,

                        'balance_due' => $invoice->balance_due,

                        'status' => $invoice->status,
                    ]
                );

        return Inertia::render(
            'portal/projects/show',
            [
                'project' => [
                    'public_id' => $project->public_id,

                    'project_number' => $project->project_number,

                    'name' => $project->name,

                    'status' => $project->status,

                    'description' => $project
                        ->getAttribute(
                            'description'
                        ),

                    'planned_start_date' => $project
                        ->getAttribute(
                            'planned_start_date'
                        ),

                    'planned_end_date' => $project
                        ->getAttribute(
                            'planned_end_date'
                        ),
                ],

                'milestones' => $milestones,

                'tasks' => $tasks,

                'documents' => $documents,

                'invoices' => $invoices,
            ]
        );
    }

    public function pdf(
        Project $project
    ): HttpResponse {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $this->assertProjectAccess(
            $project,
            $portalUser->client_id
        );

        $project->load([
            'client',
            'milestones',
        ]);

        return Pdf::loadView(
            'pdf.client-project-summary',
            [
                'project' => $project,

                'company' => CompanySetting::first(),

                'generatedAt' => now(),
            ]
        )->download(
            "{$project->project_number}-project-summary.pdf"
        );
    }

    private function assertProjectAccess(
        Project $project,
        int $clientId
    ): void {
        abort_unless(
            $project->client_id
            === $clientId,
            404
        );
    }
}
