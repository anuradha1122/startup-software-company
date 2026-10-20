<?php

namespace App\Services\Procurement;

use App\Models\ProcurementRequest;
use App\Models\User;
use App\Notifications\ProcurementRequestReviewedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProcurementReviewService
{
    public function review(
        ProcurementRequest $request,
        User $reviewer,
        string $decision,
        ?string $notes
    ): ProcurementRequest {
        $result =
            DB::transaction(
                function () use (
                    $request,
                    $reviewer,
                    $decision,
                    $notes
                ): ProcurementRequest {
                    $locked =
                        ProcurementRequest::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $request->id
                            );

                    if (
                        $locked->status
                        !== 'submitted'
                    ) {
                        throw ValidationException::withMessages([
                            'status' => 'Only submitted requests may be reviewed.',
                        ]);
                    }

                    if (
                        $locked->requested_by
                        === $reviewer->id
                    ) {
                        throw ValidationException::withMessages([
                            'reviewer' => 'A requester cannot review their own procurement request.',
                        ]);
                    }

                    if (
                        ! in_array(
                            $decision,
                            [
                                'approved',
                                'rejected',
                            ],
                            true
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'decision' => 'Invalid procurement review decision.',
                        ]);
                    }

                    if (
                        $decision === 'rejected'
                        && blank($notes)
                    ) {
                        throw ValidationException::withMessages([
                            'review_notes' => 'Review notes are required when rejecting.',
                        ]);
                    }

                    $locked->update([
                        'status' => $decision,

                        'reviewed_by' => $reviewer->id,

                        'review_notes' => $notes,

                        'reviewed_at' => now(),

                        'approved_at' => $decision === 'approved'
                                ? now()
                                : null,

                        'rejected_at' => $decision === 'rejected'
                                ? now()
                                : null,
                    ]);

                    return $locked->refresh();
                }
            );

        DB::afterCommit(
            fn () => $result
                ->requester
                ->notify(
                    new ProcurementRequestReviewedNotification(
                        $result
                    )
                )
        );

        return $result;
    }
}
