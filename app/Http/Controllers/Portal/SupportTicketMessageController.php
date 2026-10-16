<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\StoreSupportTicketMessageRequest;
use App\Models\SupportTicket;
use App\Services\Support\SupportTicketMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SupportTicketMessageController extends Controller
{
    public function store(
        StoreSupportTicketMessageRequest $request,
        SupportTicket $ticket,
        SupportTicketMessageService $service
    ): RedirectResponse {
        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        $service->clientReply(
            $ticket,
            $portalUser,
            $request->validated(
                'message'
            )
        );

        return back()->with(
            'success',
            'Reply added.'
        );
    }
}
