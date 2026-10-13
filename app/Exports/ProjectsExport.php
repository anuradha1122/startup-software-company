<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;

class ProjectsExport implements FromArray
{
    public function array(): array
    {
        $rows = [
            [
                'Project Number',
                'Project',
                'Client',
                'Project Manager',
                'Status',
                'Health',
                'Currency',
                'Contract Value',
                'Planned Start',
                'Planned End',
            ],
        ];

        $projects =
            Project::query()
                ->with([
                    'client',
                    'projectManager',
                ])
                ->orderBy(
                    'project_number'
                )
                ->get();

        foreach ($projects as $project) {
            $rows[] = [
                $project->project_number,
                $project->name,
                $project->client->name,
                $project->projectManager->name,
                $project->status,
                $project->health,
                $project->currency,
                $project->contract_value,
                $project
                    ->planned_start_date
                    ?->format('Y-m-d'),
                $project
                    ->planned_end_date
                    ?->format('Y-m-d'),
            ];
        }

        return $rows;
    }
}
