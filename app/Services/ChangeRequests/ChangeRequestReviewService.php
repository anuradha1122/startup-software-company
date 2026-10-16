<?php

namespace App\Services\ChangeRequests;

use App\Models\ChangeRequest;
use App\Models\User;
use App\Notifications\ChangeRequestDecisionNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ChangeRequestReviewService
{
    public function decide(
        ChangeRequest $changeRequest,
        User $reviewer,
        string $decision,
        ?string $reviewNotes
    ): ChangeRequest {
        return DB::transaction(
            function () use (
                $changeRequest,
                $reviewer,
                $decision,
                $reviewNotes
            ): ChangeRequest {
                $locked =
                    ChangeRequest::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $changeRequest->id
                        );

                if (
                    ! in_array(
                        $locked->status,
                        [
                            'submitted',
                            'under_review',
                            'awaiting_client',
                        ],
                        true
                    )
                ) {
                    throw new RuntimeException(
                        'This change request cannot be reviewed in its current state.'
                    );
                }

                if (
                    $decision === 'rejected'
                    && blank(
                        $reviewNotes
                    )
                ) {
                    throw new RuntimeException(
                        'Review notes are required when rejecting a change request.'
                    );
                }

                if (
                    $decision === 'approved'
                    && ! $locked
                        ->estimate()
                        ->exists()
                ) {
                    throw new RuntimeException(
                        'A change request must have an estimate before approval.'
                    );
                }

                $locked->update([
                    'status' => $decision,

                    'reviewed_by' => $reviewer->id,

                    'review_notes' => $reviewNotes,

                    'reviewed_at' => now(),

                    'approved_at' => $decision === 'approved'
                            ? now()
                            : null,

                    'rejected_at' => $decision === 'rejected'
                            ? now()
                            : null,
                ]);

                if ($locked->portalUser) {
                    $locked
                        ->portalUser
                        ->notify(
                            new ChangeRequestDecisionNotification(
                                $locked->fresh(
                                    'estimate'
                                )
                            )
                        );
                }

                return $locked->refresh();
            }
        );
    }
}
