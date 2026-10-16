<?php

namespace App\Services\Operations;

use App\Models\User;
use App\Notifications\OperationalHealthAlertNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class OperationalHealthAlertService
{
    public function __construct(
        private readonly OperationalHealthService $health
    ) {}

    public function run(): array
    {
        $result =
            $this->health->check();

        if (
            $result['status']
            === 'healthy'
        ) {
            return $result;
        }

        $cacheKey =
            'm13:operational-health-alert:'
            .today()->toDateString();

        $shouldSend =
            Cache::add(
                $cacheKey,
                true,
                now()->endOfDay()
            );

        if (! $shouldSend) {
            return $result;
        }

        $recipients =
            User::query()
                ->permission(
                    'admin-operations.alerts.receive'
                )
                ->where(
                    'status',
                    'active'
                )
                ->get();

        Notification::send(
            $recipients,
            new OperationalHealthAlertNotification(
                $result
            )
        );

        return $result;
    }
}
