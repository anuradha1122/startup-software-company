<?php

namespace App\Console\Commands;

use App\Models\SupportTicket;
use Illuminate\Console\Command;

class CheckSupportSla extends Command
{
    protected $signature =
        'support:check-sla';

    protected $description =
        'Check support tickets for overdue response and resolution SLA deadlines';

    public function handle(): int
    {
        $responseBreaches =
            SupportTicket::query()
                ->whereNull(
                    'first_responded_at'
                )
                ->whereNotIn(
                    'status',
                    [
                        'resolved',
                        'closed',
                        'cancelled',
                    ]
                )
                ->whereNotNull(
                    'first_response_due_at'
                )
                ->where(
                    'first_response_due_at',
                    '<',
                    now()
                )
                ->count();

        $resolutionBreaches =
            SupportTicket::query()
                ->whereNotIn(
                    'status',
                    [
                        'resolved',
                        'closed',
                        'cancelled',
                    ]
                )
                ->whereNotNull(
                    'resolution_due_at'
                )
                ->where(
                    'resolution_due_at',
                    '<',
                    now()
                )
                ->count();

        $this->info(
            "Response SLA breaches: {$responseBreaches}"
        );

        $this->info(
            "Resolution SLA breaches: {$resolutionBreaches}"
        );

        return self::SUCCESS;
    }
}
