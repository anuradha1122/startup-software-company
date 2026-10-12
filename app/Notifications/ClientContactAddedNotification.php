<?php

namespace App\Notifications;

use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientContactAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Client $client,
        private readonly ClientContact $contact
    ) {}

    public function via(object $notifiable): array
    {
        return [
            'mail',
            'database',
        ];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        return (new MailMessage)
            ->subject(
                "Contact added: {$this->client->name}"
            )
            ->line(
                "A new contact was added to {$this->client->name}."
            )
            ->line(
                "Contact: {$this->contact->name}"
            )
            ->action(
                'Open Client',
                url(
                    "/admin/clients/{$this->client->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'client-contact-added',
            'title' => 'Client contact added',
            'message' => "{$this->contact->name} added to {$this->client->name}.",
            'url' => "/admin/clients/{$this->client->public_id}",
        ];
    }
}
