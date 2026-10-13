<?php

namespace App\Policies;

use App\Models\ProjectAssignment;
use App\Models\User;

class ProjectAssignmentPolicy
{
    public function view(
        User $user,
        ProjectAssignment $assignment
    ): bool {
        return $user->can(
            'project-assignments.view'
        );
    }

    public function update(
        User $user,
        ProjectAssignment $assignment
    ): bool {
        return $user->can(
            'project-assignments.update'
        )
            && ! in_array(
                $assignment->status,
                [
                    'ended',
                    'cancelled',
                ],
                true
            );
    }
}
