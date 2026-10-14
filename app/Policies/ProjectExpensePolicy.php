<?php

namespace App\Policies;

use App\Models\ProjectExpense;
use App\Models\User;

class ProjectExpensePolicy
{
    public function view(
        User $user,
        ProjectExpense $expense
    ): bool {
        return $user->can(
            'expenses.view-all'
        )
            || (
                $user->can(
                    'expenses.view-own'
                )
                && (
                    $expense->created_by
                    === $user->id
                    || $expense->incurred_by
                    === $user->id
                )
            );
    }

    public function update(
        User $user,
        ProjectExpense $expense
    ): bool {
        return $user->can(
            'expenses.update'
        )
            && $expense->isEditable();
    }

    public function delete(
        User $user,
        ProjectExpense $expense
    ): bool {
        return $user->can(
            'expenses.delete'
        )
            && $expense->status
            === 'draft';
    }

    public function submit(
        User $user,
        ProjectExpense $expense
    ): bool {
        return $user->can(
            'expenses.submit'
        )
            && $expense->isEditable();
    }

    public function review(
        User $user,
        ProjectExpense $expense
    ): bool {
        return $user->can(
            'expenses.review'
        )
            && $expense->status
            === 'submitted';
    }
}
