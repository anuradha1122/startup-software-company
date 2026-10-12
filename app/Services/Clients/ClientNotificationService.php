<?php

namespace App\Services\Clients;

use App\Models\Client;
use App\Models\ClientContact;
use App\Models\User;
use App\Notifications\ClientContactAddedNotification;
use App\Notifications\ClientCreatedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class ClientNotificationService
{
    public function clientCreated(
        Client $client
    ): void {
        Notification::send(
            $this->recipients($client),
            new ClientCreatedNotification(
                $client
            )
        );
    }

    public function contactAdded(
        Client $client,
        ClientContact $contact
    ): void {
        Notification::send(
            $this->recipients($client),
            new ClientContactAddedNotification(
                $client,
                $contact
            )
        );
    }

    private function recipients(
        Client $client
    ): Collection {
        $recipients = collect();

        if ($client->owner) {
            $recipients->push(
                $client->owner
            );
        }

        User::role([
            'Director',
            'Admin',
        ])
            ->where(
                'status',
                'active'
            )
            ->get()
            ->each(
                fn (User $user) => $recipients->push($user)
            );

        return $recipients
            ->unique('id')
            ->values();
    }
}
