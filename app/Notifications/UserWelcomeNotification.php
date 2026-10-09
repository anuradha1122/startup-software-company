<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly array $roles = [],
    ) {}

    public function via(object $notifiable): array
    {
        return [
            'mail',
            'database',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to '.config('app.name'))
            ->greeting("Hello {$notifiable->name},")
            ->line(
                'An account has been created for you in '
                .config('app.name').'.'
            )
            ->line(
                'Assigned role(s): '
                .implode(', ', $this->roles)
            )
            ->line(
                'A separate secure password setup email '
                .'has been sent to your email address.'
            )
            ->action(
                'Open '.config('app.name'),
                url('/login')
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'account-created',
            'title' => 'Account created',
            'message' => 'Your IT Hub account is ready.',
            'roles' => $this->roles,
            'url' => '/dashboard',
        ];
    }
}
