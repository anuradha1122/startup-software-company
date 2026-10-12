<?php

namespace App\Services\Commercial;

use App\Models\Estimate;
use App\Models\RequirementVersion;
use RuntimeException;

class CommercialSourceValidator
{
    public function validate(
        RequirementVersion $version,
        Estimate $estimate
    ): void {
        if ($version->status !== 'approved') {
            throw new RuntimeException(
                'Only an approved requirement version may be used.'
            );
        }

        if ($estimate->status !== 'approved') {
            throw new RuntimeException(
                'Only an approved estimate may be used.'
            );
        }

        if (
            $estimate->requirement_version_id
            !== $version->id
        ) {
            throw new RuntimeException(
                'The estimate does not belong to the selected requirement version.'
            );
        }
    }
}
