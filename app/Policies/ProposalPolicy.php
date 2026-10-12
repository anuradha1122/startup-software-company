<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;

class ProposalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(
            'proposals.view'
        );
    }

    public function view(
        User $user,
        Proposal $proposal
    ): bool {
        return $user->can(
            'proposals.view'
        );
    }

    public function update(
        User $user,
        Proposal $proposal
    ): bool {
        return $user->can(
            'proposals.update'
        )
            && $proposal->status === 'draft';
    }

    public function approve(
        User $user,
        Proposal $proposal
    ): bool {
        return $user->can(
            'proposals.approve'
        )
            && $proposal->status === 'draft';
    }
}
