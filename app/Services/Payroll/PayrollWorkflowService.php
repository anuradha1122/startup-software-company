<?php

namespace App\Services\Payroll;

use App\Models\PayrollPeriod;
use App\Models\User;
use App\Notifications\PayrollPublishedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class PayrollWorkflowService
{
    public function approve(
        PayrollPeriod $period,
        User $user
    ): PayrollPeriod {
        return DB::transaction(
            function () use (
                $period,
                $user
            ): PayrollPeriod {
                $locked =
                    PayrollPeriod::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $period->id
                        );

                if (
                    $locked->status
                    !== 'calculated'
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'Only calculated payroll may be approved.',
                    ]);
                }

                $locked->update([
                    'status' => 'approved',

                    'approved_by' => $user->id,

                    'approved_at' => now(),
                ]);

                return $locked->refresh();
            }
        );
    }

    public function publish(
        PayrollPeriod $period,
        User $user
    ): PayrollPeriod {
        return DB::transaction(
            function () use (
                $period,
                $user
            ): PayrollPeriod {
                $locked =
                    PayrollPeriod::query()
                        ->with(
                            'entries.employee.user'
                        )
                        ->lockForUpdate()
                        ->findOrFail(
                            $period->id
                        );

                if (
                    $locked->status
                    !== 'approved'
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'Only approved payroll may be published.',
                    ]);
                }

                $locked->update([
                    'status' => 'published',

                    'published_by' => $user->id,

                    'published_at' => now(),
                ]);

                $recipients =
                    $locked
                        ->entries
                        ->map(
                            fn ($entry) => $entry
                                ->employee
                                ->user
                        )
                        ->filter()
                        ->unique('id')
                        ->values();

                Notification::send(
                    $recipients,
                    new PayrollPublishedNotification(
                        $locked
                    )
                );

                return $locked->refresh();
            }
        );
    }
}
