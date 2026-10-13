<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;

class ProjectAssignmentsExport implements FromArray
{
    public function __construct(
        private readonly Project $project
    ) {}

    public function array(): array
    {
        $this->project->load([
            'assignments.user.resourceProfile',
        ]);

        $rows = [
            [
                'Project',
                'Person',
                'Email',
                'Job Title',
                'Department',
                'Project Role',
                'Allocation %',
                'Status',
                'Start Date',
                'End Date',
                'Billable',
            ],
        ];

        foreach (
            $this->project
                ->assignments as $assignment
        ) {
            $rows[] = [
                $this->project
                    ->project_number,

                $assignment
                    ->user
                    ->name,

                $assignment
                    ->user
                    ->email,

                $assignment
                    ->user
                    ->resourceProfile
                    ?->job_title,

                $assignment
                    ->user
                    ->resourceProfile
                    ?->department,

                $assignment->role,

                $assignment
                    ->allocation_percent,

                $assignment->status,

                $assignment
                    ->start_date
                    ?->format('Y-m-d'),

                $assignment
                    ->end_date
                    ?->format('Y-m-d'),

                $assignment->billable
                    ? 'Yes'
                    : 'No',
            ];
        }

        return $rows;
    }
}
