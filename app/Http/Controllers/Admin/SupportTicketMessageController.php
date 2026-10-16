<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSupportTicketMessageRequest;
use App\Models\SupportTicket;
use App\Services\Support\SupportTicketMessageService;
use Illuminate\Http\RedirectResponse;

class SupportTicketMessageController extends Controller
{
    public function store(
        StoreSupportTicketMessageRequest $request,
        SupportTicket $ticket,
        SupportTicketMessageService $service
    ): RedirectResponse {
        $service->internalReply(
            $ticket,
            $request->user(),
            $request->validated(
                'message'
            ),
            $request->boolean(
                'internal_only'
            )
        );

        return back()->with(
            'success',
            'Reply added.'
        );
    }
}
