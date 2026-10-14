<?php

namespace App\Notifications;

use App\Models\ProjectExpense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectExpenseRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ProjectExpense $expense
    ) {}

    public function via(
        object $notifiable
    ): array {
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
                "Expense changes required: {$this->expense->expense_number}"
            )
            ->line(
                'Your project expense requires changes.'
            )
            ->line(
                $this->expense->review_notes
                ?? 'Please review the expense.'
            )
            ->action(
                'Open Expense',
                url(
                    "/admin/expenses/{$this->expense->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'project-expense-rejected',

            'title' => 'Expense changes required',

            'message' => "{$this->expense->expense_number} requires changes.",

            'url' => "/admin/expenses/{$this->expense->public_id}",
        ];
    }
}
