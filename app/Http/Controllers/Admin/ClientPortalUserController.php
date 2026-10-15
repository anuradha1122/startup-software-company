<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientPortalUserRequest;
use App\Http\Requests\Admin\UpdateClientPortalUserRequest;
use App\Models\Client;
use App\Models\ClientPortalUser;
use App\Services\Portal\ClientPortalInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientPortalUserController extends Controller
{
    public function index(
        Request $request,
        Client $client
    ): Response {
        abort_unless(
            $request->user()->can(
                'client-portal-users.view'
            ),
            403
        );

        return Inertia::render(
            'admin/clients/portal-users/index',
            [
                'client' => [
                    'public_id' => $client->public_id,

                    'client_number' => $client->client_number,

                    'name' => $client->name,
                ],

                'portalUsers' => $client
                    ->portalUsers()
                    ->with('contact')
                    ->orderBy('name')
                    ->get(),

                'contacts' => $client
                    ->contacts()
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderBy(
                        'name'
                    )
                    ->get([
                        'id',
                        'public_id',
                        'name',
                        'email',
                    ]),
            ]
        );
    }

    public function store(
        StoreClientPortalUserRequest $request,
        Client $client,
        ClientPortalInvitationService $service
    ): RedirectResponse {
        $data =
            $request->validated();

        if (
            ! empty(
                $data['client_contact_id']
            )
        ) {
            abort_unless(
                $client
                    ->contacts()
                    ->whereKey(
                        $data[
                            'client_contact_id'
                        ]
                    )
                    ->exists(),
                422,
                'The selected contact does not belong to this client.'
            );
        }

        $service->invite(
            $client,
            $data,
            $request->user()
        );

        return back()->with(
            'success',
            'Client portal invitation sent.'
        );
    }

    public function update(
        UpdateClientPortalUserRequest $request,
        Client $client,
        ClientPortalUser $portalUser
    ): RedirectResponse {
        abort_unless(
            $portalUser->client_id
            === $client->id,
            404
        );

        $portalUser->update(
            $request->validated()
        );

        return back()->with(
            'success',
            'Portal user updated.'
        );
    }

    public function resend(
        Request $request,
        Client $client,
        ClientPortalUser $portalUser,
        ClientPortalInvitationService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'client-portal-users.create'
            ),
            403
        );

        abort_unless(
            $portalUser->client_id
            === $client->id,
            404
        );

        abort_if(
            $portalUser->status === 'revoked',
            409
        );

        $service->resend(
            $portalUser
        );

        return back()->with(
            'success',
            'Portal invitation resent.'
        );
    }

    public function revoke(
        Request $request,
        Client $client,
        ClientPortalUser $portalUser
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'client-portal-users.revoke'
            ),
            403
        );

        abort_unless(
            $portalUser->client_id
            === $client->id,
            404
        );

        $portalUser->update([
            'status' => 'revoked',

            'remember_token' => null,
        ]);

        return back()->with(
            'success',
            'Portal access revoked.'
        );
    }
}
