<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->can(
            'invoices.view'
        );
    }

    public function view(
        User $user,
        Invoice $invoice
    ): bool {
        return $user->can(
            'invoices.view'
        );
    }

    public function update(
        User $user,
        Invoice $invoice
    ): bool {
        return $user->can(
            'invoices.update'
        )
            && $invoice->isEditable();
    }

    public function delete(
        User $user,
        Invoice $invoice
    ): bool {
        return $user->can(
            'invoices.delete'
        )
            && $invoice->status
            === 'draft';
    }

    public function issue(
        User $user,
        Invoice $invoice
    ): bool {
        return $user->can(
            'invoices.issue'
        )
            && $invoice->status
            === 'draft';
    }
}
