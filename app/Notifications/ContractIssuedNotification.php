<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractIssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Contract $contract
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(
        object $notifiable
    ): MailMessage {
        return (new MailMessage)
            ->subject(
                "Contract {$this->contract->contract_number}"
            )
            ->line(
                'The contract has been issued.'
            )
            ->line(
                $this->contract->title
            );
    }
}
