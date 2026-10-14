<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\TimesheetEntry;
use Maatwebsite\Excel\Concerns\FromArray;

class ProjectInternalCostExport implements FromArray
{
    public function __construct(
        private readonly Project $project
    ) {}

    public function array(): array
    {
        $entries =
            TimesheetEntry::query()
                ->where(
                    'project_id',
                    $this->project->id
                )
                ->whereHas(
                    'timesheet',
                    fn ($query) => $query->where(
                        'status',
                        'approved'
                    )
                )
                ->with([
                    'timesheet.user',
                    'task',
                ])
                ->orderBy(
                    'work_date'
                )
                ->get();

        $rows = [
            [
                'Date',
                'Person',
                'Task',
                'Hours',
                'Cost Rate',
                'Currency',
                'Internal Cost',
            ],
        ];

        foreach (
            $entries as $entry
        ) {
            $rows[] = [
                $entry
                    ->work_date
                    ->format('Y-m-d'),

                $entry
                    ->timesheet
                    ->user
                    ->name,

                $entry
                    ->task
                    ->task_number,

                $entry->hours,

                $entry
                    ->hourly_cost_rate_snapshot,

                $entry->cost_currency,

                $entry
                    ->internal_cost_amount,
            ];
        }

        $rows[] = [];

        $rows[] = [
            'Total Hours',
            $entries->sum('hours'),
        ];

        $rows[] = [
            'Total Internal Cost',
            $entries->sum(
                'internal_cost_amount'
            ),
        ];

        return $rows;
    }
}
