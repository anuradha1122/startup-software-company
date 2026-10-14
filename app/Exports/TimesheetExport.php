<?php

namespace App\Exports;

use App\Models\Timesheet;
use Maatwebsite\Excel\Concerns\FromArray;

class TimesheetExport implements FromArray
{
    public function __construct(
        private readonly Timesheet $timesheet
    ) {}

    public function array(): array
    {
        $this->timesheet->load([
            'user',
            'entries.project',
            'entries.task',
        ]);

        $rows = [
            [
                'Timesheet',
                $this->timesheet
                    ->user
                    ->name,
            ],

            [
                'Week',
                $this->timesheet
                    ->week_start
                    ->format('Y-m-d')
                .' to '.
                $this->timesheet
                    ->week_end
                    ->format('Y-m-d'),
            ],

            [
                'Status',
                $this->timesheet->status,
            ],

            [],
            [
                'Date',
                'Project',
                'Task',
                'Hours',
                'Billable',
                'Description',
            ],
        ];

        foreach (
            $this->timesheet->entries as $entry
        ) {
            $rows[] = [
                $entry
                    ->work_date
                    ->format('Y-m-d'),

                $entry
                    ->project
                    ->project_number,

                $entry
                    ->task
                    ->task_number,

                $entry->hours,

                $entry->billable
                    ? 'Yes'
                    : 'No',

                $entry->description,
            ];
        }

        $rows[] = [];

        $rows[] = [
            'Total Hours',
            $this->timesheet
                ->totalHours(),
        ];

        return $rows;
    }
}
