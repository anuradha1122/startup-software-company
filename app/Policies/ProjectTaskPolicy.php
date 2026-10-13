<?php

namespace App\Policies;

use App\Models\ProjectTask;
use App\Models\User;

class ProjectTaskPolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->can(
            'tasks.view'
        );
    }

    public function view(
        User $user,
        ProjectTask $task
    ): bool {
        return $user->can(
            'tasks.view'
        );
    }

    public function update(
        User $user,
        ProjectTask $task
    ): bool {
        return $user->can(
            'tasks.update'
        )
            && $task->project->status
            === 'active';
    }

    public function delete(
        User $user,
        ProjectTask $task
    ): bool {
        return $user->can(
            'tasks.delete'
        )
            && $task->submissions()
                ->doesntExist();
    }
}
