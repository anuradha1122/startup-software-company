<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientPortalResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $token
    ) {}

    public function via(
        object $notifiable
    ): array {
        return ['mail'];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        $url =
            route(
                'portal.password.reset',
                [
                    'token' => $this->token,

                    'email' => $notifiable->email,
                ]
            );

        return (new MailMessage)
            ->subject(
                'Set your client portal password'
            )
            ->line(
                'A client portal account has been created for you.'
            )
            ->action(
                'Set Portal Password',
                $url
            )
            ->line(
                'This link expires according to the portal password reset policy.'
            );
    }
}
