<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(
            'quotations.view'
        );
    }

    public function view(
        User $user,
        Quotation $quotation
    ): bool {
        return $user->can(
            'quotations.view'
        );
    }

    public function update(
        User $user,
        Quotation $quotation
    ): bool {
        return $user->can(
            'quotations.update'
        )
            && $quotation->status === 'draft';
    }

    public function approve(
        User $user,
        Quotation $quotation
    ): bool {
        return $user->can(
            'quotations.approve'
        )
            && $quotation->status === 'draft';
    }
}
