<?php

namespace App\Services\Portal;

use App\Models\ClientApprovalRequest;
use App\Models\ClientPortalUser;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ClientApprovalRequestedNotification;
use App\Notifications\ClientApprovalRespondedNotification;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

class ClientApprovalService
{
    public function __construct(
        private readonly DocumentNumberService $numbers
    ) {}

    public function request(
        Project $project,
        array $data,
        User $requester
    ): ClientApprovalRequest {
        return DB::transaction(
            function () use (
                $project,
                $data,
                $requester
            ): ClientApprovalRequest {
                $approval =
                    ClientApprovalRequest::create([
                        'approval_number' => $this->numbers->next(
                            'client_approval'
                        ),

                        'client_id' => $project->client_id,

                        'project_id' => $project->id,

                        'type' => $data['type'],

                        'title' => $data['title'],

                        'summary' => $data['summary']
                            ?? null,

                        'source_type' => $data['source_type']
                            ?? null,

                        'source_id' => $data['source_id']
                            ?? null,

                        'status' => 'pending',

                        'requested_by' => $requester->id,

                        'requested_at' => now(),

                        'due_at' => $data['due_at']
                            ?? null,
                    ]);

                $recipients =
                    ClientPortalUser::query()
                        ->where(
                            'client_id',
                            $project->client_id
                        )
                        ->where(
                            'status',
                            'active'
                        )
                        ->get();

                Notification::send(
                    $recipients,
                    new ClientApprovalRequestedNotification(
                        $approval
                    )
                );

                return $approval;
            }
        );
    }

    public function respond(
        ClientApprovalRequest $approval,
        ClientPortalUser $portalUser,
        string $decision,
        ?string $comment
    ): ClientApprovalRequest {
        if (! $approval->isPending()) {
            throw new RuntimeException(
                'This approval request is no longer pending.'
            );
        }

        if (
            $approval->client_id
            !== $portalUser->client_id
        ) {
            throw new RuntimeException(
                'This approval request does not belong to your client account.'
            );
        }

        if (
            $decision === 'rejected'
            && blank($comment)
        ) {
            throw new RuntimeException(
                'A comment is required when rejecting an approval request.'
            );
        }

        return DB::transaction(
            function () use (
                $approval,
                $portalUser,
                $decision,
                $comment
            ): ClientApprovalRequest {
                $locked =
                    ClientApprovalRequest::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $approval->id
                        );

                if (! $locked->isPending()) {
                    throw new RuntimeException(
                        'This approval request has already been answered.'
                    );
                }

                $locked->update([
                    'status' => $decision,

                    'responded_by' => $portalUser->id,

                    'response_comment' => $comment,

                    'responded_at' => now(),
                ]);

                if ($locked->requester) {
                    $locked
                        ->requester
                        ->notify(
                            new ClientApprovalRespondedNotification(
                                $locked->fresh([
                                    'project',
                                    'responder',
                                ])
                            )
                        );
                }

                return $locked->refresh();
            }
        );
    }

    public function cancel(
        ClientApprovalRequest $approval
    ): ClientApprovalRequest {
        if (! $approval->isPending()) {
            throw new RuntimeException(
                'Only pending approval requests may be cancelled.'
            );
        }

        $approval->update([
            'status' => 'cancelled',
        ]);

        return $approval->refresh();
    }
}
