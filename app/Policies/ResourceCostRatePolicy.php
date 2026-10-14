<?php

namespace App\Policies;

use App\Models\ResourceCostRate;
use App\Models\User;

class ResourceCostRatePolicy
{
    public function view(
        User $user,
        ResourceCostRate $rate
    ): bool {
        return $user->can(
            'internal-costs.view'
        );
    }

    public function create(
        User $user
    ): bool {
        return $user->can(
            'internal-costs.manage-rates'
        );
    }
}
