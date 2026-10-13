<?php

namespace App\Services\Projects;

use App\Models\Project;
use App\Models\ProjectRisk;

class ProjectRiskService
{
    public function create(
        Project $project,
        array $data
    ): ProjectRisk {
        $next =
            $project
                ->risks()
                ->count() + 1;

        return $project
            ->risks()
            ->create([
                ...$data,

                'risk_number' => sprintf(
                    'RISK-%03d',
                    $next
                ),

                'score' => (int) $data['likelihood']
                    * (int) $data['impact'],
            ]);
    }
}
