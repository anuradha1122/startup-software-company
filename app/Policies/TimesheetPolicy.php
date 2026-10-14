<?php

namespace App\Policies;

use App\Models\Timesheet;
use App\Models\User;

class TimesheetPolicy
{
    public function viewAny(
        User $user
    ): bool {
        return $user->can(
            'timesheets.view-own'
        )
            || $user->can(
                'timesheets.view-all'
            );
    }

    public function view(
        User $user,
        Timesheet $timesheet
    ): bool {
        return $user->can(
            'timesheets.view-all'
        )
            || (
                $user->can(
                    'timesheets.view-own'
                )
                && $timesheet->user_id
                === $user->id
            );
    }

    public function update(
        User $user,
        Timesheet $timesheet
    ): bool {
        return $user->can(
            'timesheets.update'
        )
            && $timesheet->user_id
            === $user->id
            && $timesheet
                ->isEditable();
    }

    public function submit(
        User $user,
        Timesheet $timesheet
    ): bool {
        return $user->can(
            'timesheets.submit'
        )
            && $timesheet->user_id
            === $user->id
            && $timesheet
                ->isEditable();
    }

    public function review(
        User $user,
        Timesheet $timesheet
    ): bool {
        return $user->can(
            'timesheets.review'
        )
            && $timesheet->status
            === 'submitted';
    }
}
