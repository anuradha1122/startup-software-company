<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OperationalHealthAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly array $health
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
        $mail =
            (new MailMessage)
                ->subject(
                    'IT Hub operational health warning'
                )
                ->line(
                    'The operational health check detected items requiring attention.'
                )
                ->line(
                    "Status: {$this->health['status']}"
                )
                ->line(
                    "Issues: {$this->health['issue_count']}"
                );

        foreach (
            $this->health['checks'] as $check
        ) {
            if (
                in_array(
                    $check['status'],
                    [
                        'warning',
                        'critical',
                    ],
                    true
                )
            ) {
                $mail->line(
                    "{$check['label']}: {$check['message']}"
                );
            }
        }

        return $mail->action(
            'Open Admin Operations',
            url('/admin/operations')
        );
    }

    public function toArray(
        object $notifiable
    ): array {
        return [
            'type' => 'operational-health-warning',

            'title' => 'Operational health warning',

            'message' => "{$this->health['issue_count']} operational issue(s) require review.",

            'status' => $this->health[
                    'status'
                ],

            'issue_count' => $this->health[
                    'issue_count'
                ],

            'url' => '/admin/operations',
        ];
    }
}
