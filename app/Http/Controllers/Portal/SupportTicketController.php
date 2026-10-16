<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreSupportTicketRequest;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Services\Support\SupportTicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SupportTicketController extends Controller
{
    public function index(): Response
    {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $tickets =
            SupportTicket::query()
                ->where(
                    'client_id',
                    $portalUser->client_id
                )
                ->latest()
                ->get()
                ->map(
                    fn (
                        SupportTicket $ticket
                    ) => [
                        'public_id' => $ticket->public_id,

                        'ticket_number' => $ticket->ticket_number,

                        'subject' => $ticket->subject,

                        'priority' => $ticket->priority,

                        'severity' => $ticket->severity,

                        'status' => $ticket->status,

                        'created_at' => $ticket->created_at,
                    ]
                );

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
                ->get([
                    'id',
                    'public_id',
                    'project_number',
                    'name',
                ]);

        return Inertia::render(
            'portal/support/index',
            [
                'tickets' => $tickets,

                'projects' => $projects,
            ]
        );
    }

    public function store(
        StoreSupportTicketRequest $request,
        SupportTicketService $service
    ): RedirectResponse {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $data =
            $request->validated();

        if (
            ! empty(
                $data['project_id']
            )
        ) {
            abort_unless(
                Project::query()
                    ->whereKey(
                        $data[
                            'project_id'
                        ]
                    )
                    ->where(
                        'client_id',
                        $portalUser
                            ->client_id
                    )
                    ->exists(),
                404
            );
        }

        $ticket =
            $service->createFromPortal(
                $data,
                $portalUser
            );

        return redirect()
            ->route(
                'portal.support.show',
                $ticket
            );
    }

    public function show(
        SupportTicket $ticket
    ): Response {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        abort_unless(
            $ticket->client_id
            === $portalUser->client_id,
            404
        );

        $messages =
            $ticket
                ->messages()
                ->where(
                    'internal_only',
                    false
                )
                ->with([
                    'user',
                    'portalUser',
                ])
                ->get()
                ->map(
                    fn ($message) => [
                        'public_id' => $message->public_id,

                        'author_type' => $message->author_type,

                        'author_name' => $message->author_type
                            === 'client'
                                ? $message
                                    ->portalUser
                                    ?->name
                                : $message
                                    ->user
                                    ?->name,

                        'message' => $message->message,

                        'created_at' => $message->created_at,
                    ]
                );

        return Inertia::render(
            'portal/support/show',
            [
                'ticket' => [
                    'public_id' => $ticket->public_id,

                    'ticket_number' => $ticket->ticket_number,

                    'subject' => $ticket->subject,

                    'description' => $ticket->description,

                    'priority' => $ticket->priority,

                    'severity' => $ticket->severity,

                    'status' => $ticket->status,

                    'created_at' => $ticket->created_at,
                ],

                'messages' => $messages,
            ]
        );
    }
}
