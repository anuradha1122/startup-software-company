<?php

namespace App\Exports;

use App\Models\SupportTicket;
use Maatwebsite\Excel\Concerns\FromArray;

class SupportTicketsExport implements FromArray
{
    public function array(): array
    {
        $rows = [[
            'Ticket',
            'Client',
            'Project',
            'Subject',
            'Priority',
            'Severity',
            'Status',
            'Assignee',
            'First Response Due',
            'Resolution Due',
            'Created',
        ]];

        $tickets =
            SupportTicket::query()
                ->with([
                    'client',
                    'project',
                    'assignee',
                ])
                ->latest()
                ->get();

        foreach (
            $tickets as $ticket
        ) {
            $rows[] = [
                $ticket->ticket_number,

                $ticket->client->name,

                $ticket
                    ->project
                    ?->project_number,

                $ticket->subject,

                $ticket->priority,

                $ticket->severity,

                $ticket->status,

                $ticket
                    ->assignee
                    ?->name,

                $ticket
                    ->first_response_due_at
                    ?->format(
                        'Y-m-d H:i'
                    ),

                $ticket
                    ->resolution_due_at
                    ?->format(
                        'Y-m-d H:i'
                    ),

                $ticket
                    ->created_at
                    ->format(
                        'Y-m-d H:i'
                    ),
            ];
        }

        return $rows;
    }
}
