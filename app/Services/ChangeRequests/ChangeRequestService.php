<?php

namespace App\Services\ChangeRequests;

use App\Models\ChangeRequest;
use App\Models\ClientPortalUser;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ChangeRequestSubmittedNotification;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\Notification;

class ChangeRequestService
{
    public function __construct(
        private readonly DocumentNumberService $numbers
    ) {}

    public function createInternal(
        Project $project,
        array $data,
        User $user
    ): ChangeRequest {
        return $this->create(
            $project,
            $data,
            internalUser: $user,
            portalUser: null
        );
    }

    public function createFromPortal(
        Project $project,
        array $data,
        ClientPortalUser $portalUser
    ): ChangeRequest {
        abort_unless(
            $project->client_id
            === $portalUser->client_id,
            404
        );

        return $this->create(
            $project,
            $data,
            internalUser: null,
            portalUser: $portalUser
        );
    }

    private function create(
        Project $project,
        array $data,
        ?User $internalUser,
        ?ClientPortalUser $portalUser
    ): ChangeRequest {
        $request =
            ChangeRequest::create([
                'change_request_number' => $this->numbers->next(
                    'change_request'
                ),

                'client_id' => $project->client_id,

                'project_id' => $project->id,

                'client_portal_user_id' => $portalUser?->id,

                'title' => $data['title'],

                'description' => $data['description'],

                'business_reason' => $data[
                        'business_reason'
                    ] ?? null,

                'priority' => $data['priority'],

                'status' => 'submitted',

                'submitted_at' => now(),

                'created_by' => $internalUser?->id,
            ]);

        $recipients =
            User::query()
                ->permission(
                    'change-requests.review'
                )
                ->where(
                    'status',
                    'active'
                )
                ->get();

        Notification::send(
            $recipients,
            new ChangeRequestSubmittedNotification(
                $request
            )
        );

        return $request;
    }
}
