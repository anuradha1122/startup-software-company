<?php

namespace App\Services\Projects;

use App\Models\Contract;
use App\Models\Project;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProjectSetupService
{
    public function __construct(
        private readonly DocumentNumberService $numbers
    ) {}

    public function create(
        Contract $contract,
        array $data,
        int $userId
    ): Project {
        if ($contract->status !== 'signed') {
            throw new RuntimeException(
                'A project may only be created from a signed contract.'
            );
        }

        if ($contract->project()->exists()) {
            throw new RuntimeException(
                'A project already exists for this contract.'
            );
        }

        return DB::transaction(
            function () use (
                $contract,
                $data,
                $userId
            ): Project {
                $project = Project::create([
                    'project_number' => $this->numbers->next(
                        'project'
                    ),

                    'contract_id' => $contract->id,

                    'client_id' => $contract->client_id,

                    'requirement_version_id' => $contract
                        ->requirement_version_id,

                    'estimate_id' => $contract->estimate_id,

                    'name' => $data['name'],

                    'status' => 'planned',

                    'health' => 'green',

                    'methodology' => $data['methodology']
                        ?? null,

                    'description' => $data['description']
                        ?? null,

                    'currency' => $contract->currency,

                    'contract_value' => $contract->contract_value,

                    'project_manager_id' => $data[
                            'project_manager_id'
                        ],

                    'client_sponsor_contact_id' => $data[
                            'client_sponsor_contact_id'
                        ] ?? null,

                    'planned_start_date' => $data[
                            'planned_start_date'
                        ] ?? $contract->start_date,

                    'planned_end_date' => $data[
                            'planned_end_date'
                        ] ?? $contract->end_date,

                    'created_by' => $userId,
                ]);

                $project
                    ->charters()
                    ->create([
                        'version_number' => 1,
                        'status' => 'draft',
                        'created_by' => $userId,
                    ]);

                return $project;
            }
        );
    }
}
