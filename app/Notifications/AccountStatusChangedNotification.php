<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $status,
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
            ->subject('Account status changed')
            ->greeting("Hello {$notifiable->name},")
            ->line(
                'Your '.config('app.name')
                ." account status is now: {$this->status}."
            )
            ->action(
                'Open IT Hub',
                url('/login')
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'account-status-changed',
            'title' => 'Account status changed',
            'message' => "Your account status is now {$this->status}.",
            'status' => $this->status,
            'url' => '/dashboard',
        ];
    }
}
