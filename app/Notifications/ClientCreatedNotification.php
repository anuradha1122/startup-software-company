<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Client $client
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
                "Client created: {$this->client->client_number}"
            )
            ->greeting(
                "Hello {$notifiable->name},"
            )
            ->line(
                'A client profile has been created.'
            )
            ->line(
                "{$this->client->client_number} — {$this->client->name}"
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
            'type' => 'client-created',
            'title' => 'Client created',
            'message' => "{$this->client->client_number} — {$this->client->name}",
            'url' => "/admin/clients/{$this->client->public_id}",
        ];
    }
}
