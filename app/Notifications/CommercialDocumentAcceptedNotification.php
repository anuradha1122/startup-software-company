<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommercialDocumentAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $documentType,
        private readonly string $documentNumber,
        private readonly string $url
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
                "{$this->documentType} accepted"
            )
            ->line(
                "{$this->documentNumber} has been accepted."
            )
            ->action(
                'Open Document',
                url($this->url)
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'commercial-document-accepted',

            'title' => "{$this->documentType} accepted",

            'message' => "{$this->documentNumber} has been accepted.",

            'url' => $this->url,
        ];
    }
}
