<?php

namespace App\Policies;

use App\Models\Estimate;
use App\Models\User;

class EstimatePolicy
{
    public function view(
        User $user,
        Estimate $estimate
    ): bool {
        return $user->can(
            'estimates.view'
        );
    }

    public function update(
        User $user,
        Estimate $estimate
    ): bool {
        return $user->can(
            'estimates.update'
        )
            && $estimate->status === 'draft';
    }

    public function approve(
        User $user,
        Estimate $estimate
    ): bool {
        return $user->can(
            'estimates.approve'
        )
            && $estimate->status === 'draft';
    }
}
