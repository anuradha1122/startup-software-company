<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;

class ClientApprovalsExport implements FromArray
{
    public function __construct(
        private readonly Project $project
    ) {}

    public function array(): array
    {
        $approvals =
            $this->project
                ->clientApprovalRequests()
                ->with('responder')
                ->orderBy(
                    'requested_at'
                )
                ->get();

        $rows = [[
            'Approval',
            'Type',
            'Title',
            'Status',
            'Requested At',
            'Due At',
            'Responded By',
            'Responded At',
            'Response',
        ]];

        foreach (
            $approvals as $approval
        ) {
            $rows[] = [
                $approval
                    ->approval_number,

                $approval->type,

                $approval->title,

                $approval->status,

                $approval
                    ->requested_at
                    ?->format(
                        'Y-m-d H:i'
                    ),

                $approval
                    ->due_at
                    ?->format(
                        'Y-m-d H:i'
                    ),

                $approval
                    ->responder
                    ?->name,

                $approval
                    ->responded_at
                    ?->format(
                        'Y-m-d H:i'
                    ),

                $approval
                    ->response_comment,
            ];
        }

        return $rows;
    }
}
