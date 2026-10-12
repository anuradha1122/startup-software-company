<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(
            'contracts.view'
        );
    }

    public function view(
        User $user,
        Contract $contract
    ): bool {
        return $user->can(
            'contracts.view'
        );
    }

    public function update(
        User $user,
        Contract $contract
    ): bool {
        return $user->can(
            'contracts.update'
        )
            && $contract->status === 'draft';
    }
}
