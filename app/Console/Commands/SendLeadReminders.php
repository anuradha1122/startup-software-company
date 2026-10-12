<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadFollowUpDueNotification;
use App\Notifications\LeadStaleNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class SendLeadReminders extends Command
{
    protected $signature = 'leads:send-reminders';

    protected $description =
        'Send lead follow-up and stale lead reminders';

    public function handle(): int
    {
        $this->sendFollowUpReminders();
        $this->sendStaleLeadReminders();

        return self::SUCCESS;
    }

    private function sendFollowUpReminders(): void
    {
        Lead::query()
            ->open()
            ->whereNotNull('next_follow_up_at')
            ->where(
                'next_follow_up_at',
                '<=',
                now()
            )
            ->whereNull('follow_up_reminded_at')
            ->with('owner')
            ->chunkById(
                100,
                function (Collection $leads): void {
                    foreach ($leads as $lead) {
                        $recipients =
                            $this->recipients($lead);

                        if ($recipients->isNotEmpty()) {
                            Notification::send(
                                $recipients,
                                new LeadFollowUpDueNotification(
                                    $lead
                                )
                            );
                        }

                        $lead->updateQuietly([
                            'follow_up_reminded_at' => now(),
                        ]);
                    }
                }
            );
    }

    private function sendStaleLeadReminders(): void
    {
        Lead::query()
            ->open()
            ->whereNull('stale_notified_at')
            ->where(function ($query): void {
                $query
                    ->where(
                        'last_activity_at',
                        '<=',
                        now()->subDays(7)
                    )
                    ->orWhere(function ($inner): void {
                        $inner
                            ->whereNull(
                                'last_activity_at'
                            )
                            ->where(
                                'created_at',
                                '<=',
                                now()->subDays(7)
                            );
                    });
            })
            ->with('owner')
            ->chunkById(
                100,
                function (Collection $leads): void {
                    foreach ($leads as $lead) {
                        $recipients =
                            $this->recipients($lead);

                        if ($recipients->isNotEmpty()) {
                            Notification::send(
                                $recipients,
                                new LeadStaleNotification(
                                    $lead
                                )
                            );
                        }

                        $lead->updateQuietly([
                            'stale_notified_at' => now(),
                        ]);
                    }
                }
            );
    }

    private function recipients(
        Lead $lead
    ): Collection {
        $users = collect();

        if ($lead->owner) {
            $users->push($lead->owner);
        }

        User::role([
            'Director',
            'Admin',
        ])
            ->get()
            ->each(
                fn (User $user) => $users->push($user)
            );

        return $users
            ->unique('id')
            ->values();
    }
}
