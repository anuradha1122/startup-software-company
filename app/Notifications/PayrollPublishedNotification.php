<?php

namespace App\Notifications;

use App\Models\PayrollPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayrollPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly PayrollPeriod $period
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
                "Payslip available: {$this->period->name}"
            )
            ->line(
                'Your payslip is now available.'
            )
            ->action(
                'View Payslip',
                url('/employee/payslips')
            );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'payroll-published',

            'title' => 'Payslip available',

            'message' => "{$this->period->name} payroll has been published.",

            'url' => '/employee/payslips',
        ];
    }
}
