<?php

namespace App\Services\Requirements;

use App\Models\Requirement;
use App\Models\RequirementVersion;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RequirementVersionService
{
    public function createRevision(
        Requirement $requirement,
        int $userId
    ): RequirementVersion {
        return DB::transaction(
            function () use (
                $requirement,
                $userId
            ): RequirementVersion {
                $latest = $requirement
                    ->versions()
                    ->lockForUpdate()
                    ->orderByDesc(
                        'version_number'
                    )
                    ->first();

                if (! $latest) {
                    throw new RuntimeException(
                        'No requirement version exists.'
                    );
                }

                if (
                    $latest->status
                    === 'draft'
                ) {
                    throw new RuntimeException(
                        'A draft version already exists.'
                    );
                }

                return $requirement
                    ->versions()
                    ->create([
                        'version_number' => $latest->version_number
                            + 1,

                        'status' => 'draft',

                        'version_title' => $latest->version_title,

                        'objective' => $latest->objective,

                        'scope_in' => $latest->scope_in,

                        'scope_out' => $latest->scope_out,

                        'functional_requirements' => $latest->functional_requirements,

                        'non_functional_requirements' => $latest->non_functional_requirements,

                        'assumptions' => $latest->assumptions,

                        'constraints' => $latest->constraints,

                        'acceptance_criteria' => $latest->acceptance_criteria,

                        'client_notes' => $latest->client_notes,

                        'internal_notes' => $latest->internal_notes,

                        'created_by' => $userId,
                    ]);
            }
        );
    }
}
