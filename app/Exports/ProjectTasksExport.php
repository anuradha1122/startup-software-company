<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;

class ProjectTasksExport implements FromArray
{
    public function __construct(
        private readonly Project $project
    ) {}

    public function array(): array
    {
        $this->project->load([
            'tasks.workflowStatus',
            'tasks.assignee',
            'tasks.phase',
            'tasks.milestone',
        ]);

        $rows = [
            [
                'Task',
                'Title',
                'Type',
                'Priority',
                'Status',
                'Assignee',
                'Phase',
                'Milestone',
                'Estimated Hours',
                'Planned Start',
                'Due Date',
            ],
        ];

        foreach (
            $this->project
                ->tasks
                ->sortBy(
                    'task_sequence'
                ) as $task
        ) {
            $rows[] = [
                $task->task_number,
                $task->title,
                $task->type,
                $task->priority,
                $task
                    ->workflowStatus
                    ->name,
                $task->assignee?->name,
                $task->phase?->name,
                $task->milestone?->name,
                $task->estimated_hours,
                $task
                    ->planned_start_date
                    ?->format('Y-m-d'),
                $task
                    ->due_date
                    ?->format('Y-m-d'),
            ];
        }

        return $rows;
    }
}
