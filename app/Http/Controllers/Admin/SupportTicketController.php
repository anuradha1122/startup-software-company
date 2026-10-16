<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SupportTicketsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSupportTicketRequest;
use App\Http\Requests\Admin\UpdateSupportTicketRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\Support\SupportTicketService;
use App\Services\Support\SupportTicketWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupportTicketController extends Controller
{
    public function index(
        Request $request
    ): Response {
        abort_unless(
            $request->user()->can(
                'support-tickets.view'
            ),
            403
        );

        $tickets =
            SupportTicket::query()
                ->with([
                    'client',
                    'project',
                    'assignee',
                ])
                ->when(
                    $request->filled(
                        'status'
                    ),
                    fn ($query) => $query->where(
                        'status',
                        (string) $request
                            ->string(
                                'status'
                            )
                    )
                )
                ->when(
                    $request->filled(
                        'priority'
                    ),
                    fn ($query) => $query->where(
                        'priority',
                        (string) $request
                            ->string(
                                'priority'
                            )
                    )
                )
                ->latest()
                ->paginate(25)
                ->withQueryString();

        return Inertia::render(
            'admin/support/index',
            [
                'tickets' => $tickets,

                'filters' => $request->only([
                    'status',
                    'priority',
                ]),

                'clients' => Client::query()
                    ->orderBy('name')
                    ->get([
                        'id',
                        'name',
                    ]),

                'projects' => Project::query()
                    ->orderBy(
                        'project_number'
                    )
                    ->get([
                        'id',
                        'client_id',
                        'project_number',
                        'name',
                    ]),
            ]
        );
    }

    public function store(
        StoreSupportTicketRequest $request,
        SupportTicketService $service
    ): RedirectResponse {
        $data =
            $request->validated();

        if (
            ! empty(
                $data['project_id']
            )
        ) {
            $project =
                Project::findOrFail(
                    $data['project_id']
                );

            abort_unless(
                $project->client_id
                === (int) $data[
                    'client_id'
                ],
                422,
                'The project does not belong to the selected client.'
            );
        }

        $ticket =
            $service->createInternal(
                $data,
                $request->user()
            );

        return redirect()
            ->route(
                'admin.support.show',
                $ticket
            )
            ->with(
                'success',
                'Support ticket created.'
            );
    }

    public function show(
        Request $request,
        SupportTicket $ticket
    ): Response {
        abort_unless(
            $request->user()->can(
                'support-tickets.view'
            ),
            403
        );

        $ticket->load([
            'client',
            'project',
            'assignee',
            'portalUser',
            'messages.user',
            'messages.portalUser',
        ]);

        return Inertia::render(
            'admin/support/show',
            [
                'ticket' => $ticket,

                'assignees' => User::query()
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderBy('name')
                    ->get([
                        'id',
                        'public_id',
                        'name',
                    ]),
            ]
        );
    }

    public function update(
        UpdateSupportTicketRequest $request,
        SupportTicket $ticket,
        SupportTicketWorkflowService $workflow
    ): RedirectResponse {
        $data =
            $request->validated();

        if (
            ! empty(
                $data['assigned_to']
            )
            && $ticket->assigned_to
                !== (int) $data[
                    'assigned_to'
                ]
        ) {
            abort_unless(
                $request->user()->can(
                    'support-tickets.assign'
                ),
                403
            );

            $assignee =
                User::findOrFail(
                    $data[
                        'assigned_to'
                    ]
                );

            $workflow->assign(
                $ticket,
                $assignee
            );
        }

        if (
            $ticket->status
            !== $data['status']
        ) {
            abort_unless(
                $request->user()->can(
                    'support-tickets.resolve'
                ),
                403
            );

            $workflow->transition(
                $ticket->refresh(),
                $data['status']
            );
        }

        $ticket->refresh()->update([
            'priority' => $data['priority'],

            'severity' => $data['severity'],
        ]);

        return back()->with(
            'success',
            'Support ticket updated.'
        );
    }

    public function excel(
        Request $request
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'support-tickets.export'
            ),
            403
        );

        return Excel::download(
            new SupportTicketsExport,
            'support-tickets.xlsx'
        );
    }
}
