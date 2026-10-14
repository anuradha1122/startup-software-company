<?php

namespace App\Services\Expenses;

use App\Models\ProjectExpense;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProjectExpenseService
{
    public function submit(
        ProjectExpense $expense
    ): ProjectExpense {
        if (! $expense->isEditable()) {
            throw new RuntimeException(
                'Only draft or rejected expenses may be submitted.'
            );
        }

        if ((float) $expense->total_amount <= 0) {
            throw new RuntimeException(
                'Expense total must be greater than zero.'
            );
        }

        if (
            $expense->attachments()
                ->doesntExist()
        ) {
            throw new RuntimeException(
                'At least one supporting attachment is required before submission.'
            );
        }

        $expense->update([
            'status' => 'submitted',

            'submitted_at' => now(),

            'reviewed_by' => null,

            'review_notes' => null,

            'reviewed_at' => null,
        ]);

        return $expense->refresh();
    }

    public function approve(
        ProjectExpense $expense,
        User $reviewer,
        ?string $notes = null
    ): ProjectExpense {
        if ($expense->status !== 'submitted') {
            throw new RuntimeException(
                'Only submitted expenses may be approved.'
            );
        }

        return DB::transaction(
            function () use (
                $expense,
                $reviewer,
                $notes
            ): ProjectExpense {
                $expense->update([
                    'status' => 'approved',

                    'reviewed_by' => $reviewer->id,

                    'review_notes' => $notes,

                    'reviewed_at' => now(),
                ]);

                return $expense->refresh();
            }
        );
    }

    public function reject(
        ProjectExpense $expense,
        User $reviewer,
        ?string $notes = null
    ): ProjectExpense {
        if ($expense->status !== 'submitted') {
            throw new RuntimeException(
                'Only submitted expenses may be rejected.'
            );
        }

        if (! $notes) {
            throw new RuntimeException(
                'Review notes are required when rejecting an expense.'
            );
        }

        $expense->update([
            'status' => 'rejected',

            'reviewed_by' => $reviewer->id,

            'review_notes' => $notes,

            'reviewed_at' => now(),
        ]);

        return $expense->refresh();
    }
}
