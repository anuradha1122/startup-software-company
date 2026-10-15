<?php

namespace App\Services\Portal;

use App\Models\Client;
use App\Models\ClientPortalUser;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use RuntimeException;

class ClientPortalInvitationService
{
    public function invite(
        Client $client,
        array $data,
        User $inviter
    ): ClientPortalUser {
        $portalUser =
            ClientPortalUser::create([
                'client_id' => $client->id,

                'client_contact_id' => $data[
                        'client_contact_id'
                    ] ?? null,

                'name' => $data['name'],

                'email' => strtolower(
                    $data['email']
                ),

                'status' => 'invited',

                'invited_at' => now(),

                'invited_by' => $inviter->id,
            ]);

        $status =
            Password::broker(
                'client_portal'
            )->sendResetLink([
                'email' => $portalUser->email,
            ]);

        if (
            $status
            !== Password::RESET_LINK_SENT
        ) {
            throw new RuntimeException(
                'The client portal invitation could not be sent.'
            );
        }

        return $portalUser;
    }

    public function resend(
        ClientPortalUser $portalUser
    ): void {
        $status =
            Password::broker(
                'client_portal'
            )->sendResetLink([
                'email' => $portalUser->email,
            ]);

        if (
            $status
            !== Password::RESET_LINK_SENT
        ) {
            throw new RuntimeException(
                'The client portal invitation could not be resent.'
            );
        }

        $portalUser->update([
            'invited_at' => now(),
        ]);
    }
}
