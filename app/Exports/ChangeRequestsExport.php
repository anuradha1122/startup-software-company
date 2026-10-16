<?php

namespace App\Exports;

use App\Models\ChangeRequest;
use Maatwebsite\Excel\Concerns\FromArray;

class ChangeRequestsExport implements FromArray
{
    public function array(): array
    {
        $rows = [[
            'Change Request',
            'Project',
            'Client',
            'Title',
            'Priority',
            'Status',
            'Currency',
            'Client Amount',
            'Internal Estimated Cost',
            'Estimated Hours',
            'Estimated Days',
            'Submitted',
            'Reviewed',
        ]];

        $requests =
            ChangeRequest::query()
                ->with([
                    'client',
                    'project',
                    'estimate',
                ])
                ->latest()
                ->get();

        foreach (
            $requests as $request
        ) {
            $rows[] = [
                $request
                    ->change_request_number,

                $request
                    ->project
                    ->project_number,

                $request
                    ->client
                    ->name,

                $request->title,

                $request->priority,

                $request->status,

                $request
                    ->estimate
                    ?->currency,

                $request
                    ->estimate
                    ?->client_amount,

                $request
                    ->estimate
                    ?->internal_estimated_cost,

                $request
                    ->estimate
                    ?->estimated_hours,

                $request
                    ->estimate
                    ?->estimated_days,

                $request
                    ->submitted_at
                    ?->format(
                        'Y-m-d H:i'
                    ),

                $request
                    ->reviewed_at
                    ?->format(
                        'Y-m-d H:i'
                    ),
            ];
        }

        return $rows;
    }
}
