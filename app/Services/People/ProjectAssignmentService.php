<?php

namespace App\Services\People;

use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\ResourceProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProjectAssignmentService
{
    public function create(
        Project $project,
        User $user,
        array $data,
        int $createdBy
    ): ProjectAssignment {
        if (
            $project
                ->assignments()
                ->where(
                    'user_id',
                    $user->id
                )
                ->exists()
        ) {
            throw new RuntimeException(
                'This user already has a project assignment.'
            );
        }

        if ($user->status !== 'active') {
            throw new RuntimeException(
                'Only active users can be assigned to projects.'
            );
        }

        ResourceProfile::firstOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'available_for_projects' => true,

                'updated_by' => $createdBy,
            ]
        );

        return DB::transaction(
            fn () => $project
                ->assignments()
                ->create([
                    ...$data,

                    'user_id' => $user->id,

                    'created_by' => $createdBy,
                ])
        );
    }
}
