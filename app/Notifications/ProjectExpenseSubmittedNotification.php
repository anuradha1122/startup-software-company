<?php

namespace App\Notifications;

use App\Models\ProjectExpense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectExpenseSubmittedNotification extends Notification implements ShouldQueue
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
                "Expense submitted: {$this->expense->expense_number}"
            )
            ->line(
                'A project expense is ready for review.'
            )
            ->line(
                "Project: {$this->expense->project->project_number}"
            )
            ->line(
                "Expense: {$this->expense->title}"
            )
            ->line(
                "Amount: {$this->expense->currency} {$this->expense->total_amount}"
            )
            ->action(
                'Review Expense',
                url(
                    "/admin/expenses/{$this->expense->public_id}"
                )
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'project-expense-submitted',

            'title' => 'Expense submitted',

            'message' => "{$this->expense->expense_number} is ready for review.",

            'url' => "/admin/expenses/{$this->expense->public_id}",
        ];
    }
}
