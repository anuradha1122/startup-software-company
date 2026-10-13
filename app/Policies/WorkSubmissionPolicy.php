<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkSubmission;

class WorkSubmissionPolicy
{
    public function view(
        User $user,
        WorkSubmission $submission
    ): bool {
        return $user->can(
            'work-submissions.view'
        );
    }

    public function review(
        User $user,
        WorkSubmission $submission
    ): bool {
        return $user->can(
            'work-submissions.review'
        )
            && $submission->status
            === 'submitted';
    }
}
