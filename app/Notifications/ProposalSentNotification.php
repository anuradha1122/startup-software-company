<?php

namespace App\Notifications;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Proposal $proposal
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
                "Proposal {$this->proposal->proposal_number}"
            )
            ->line(
                'A software proposal has been prepared for your review.'
            )
            ->line(
                $this->proposal->title
            )
            ->line(
                'Please contact us if you require any clarification.'
            );
    }
}
