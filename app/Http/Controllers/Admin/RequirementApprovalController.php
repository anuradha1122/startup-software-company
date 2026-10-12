<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RecordRequirementApprovalRequest;
use App\Http\Requests\Admin\RequestRequirementApprovalRequest;
use App\Models\ClientContact;
use App\Models\Requirement;
use App\Models\RequirementApproval;
use App\Models\RequirementVersion;
use App\Notifications\RequirementApprovalRequestedNotification;
use App\Notifications\RequirementRevisionRequiredNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class RequirementApprovalController extends Controller
{
    public function requestApproval(
        RequestRequirementApprovalRequest $request,
        Requirement $requirement,
        RequirementVersion $version
    ): RedirectResponse {
        $this->assertBelongs(
            $requirement,
            $version
        );

        abort_unless(
            $version->status
                === 'in_review',
            409
        );

        $contact =
            ClientContact::findOrFail(
                $request->integer(
                    'client_contact_id'
                )
            );

        abort_unless(
            $contact->client_id
                === $requirement->client_id,
            422,
            'The selected contact does not belong to this client.'
        );

        abort_unless(
            filled($contact->email),
            422,
            'The selected contact does not have an email address.'
        );

        $approval =
            DB::transaction(
                function () use (
                    $request,
                    $version,
                    $contact,
                    $requirement
                ): RequirementApproval {
                    $approval =
                        $version
                            ->approvals()
                            ->create([
                                'client_contact_id' => $contact->id,

                                'approver_name' => $contact->name,

                                'approver_email' => $contact->email,

                                'status' => 'pending',

                                'requested_by' => $request->user()
                                    ->id,

                                'requested_at' => now(),
                            ]);

                    $version->update([
                        'status' => 'pending_client_approval',

                        'submitted_for_approval_at' => now(),
                    ]);

                    $requirement->update([
                        'status' => 'pending_client_approval',
                    ]);

                    return $approval;
                }
            );

        Notification::route(
            'mail',
            [
                $approval->approver_email => $approval->approver_name,
            ]
        )->notify(
            new RequirementApprovalRequestedNotification(
                $requirement,
                $version
            )
        );

        return back()->with(
            'success',
            'Client approval requested.'
        );
    }

    public function decision(
        RecordRequirementApprovalRequest $request,
        Requirement $requirement,
        RequirementVersion $version,
        RequirementApproval $approval
    ): RedirectResponse {
        $this->assertBelongs(
            $requirement,
            $version
        );

        abort_unless(
            $approval->requirement_version_id
                === $version->id,
            404
        );

        abort_unless(
            $approval->status
                === 'pending',
            409
        );

        $decision =
            $request->validated(
                'decision'
            );

        DB::transaction(
            function () use (
                $request,
                $requirement,
                $version,
                $approval,
                $decision
            ): void {
                $approval->update([
                    'status' => $decision,

                    'decision_notes' => $request->validated(
                        'decision_notes'
                    ),

                    'recorded_by' => $request->user()->id,

                    'responded_at' => now(),
                ]);

                if (
                    $decision
                    === 'approved'
                ) {
                    $requirement
                        ->versions()
                        ->where(
                            'id',
                            '!=',
                            $version->id
                        )
                        ->where(
                            'status',
                            'approved'
                        )
                        ->update([
                            'status' => 'superseded',
                        ]);

                    $version->update([
                        'status' => 'approved',

                        'approved_at' => now(),
                    ]);

                    $requirement->update([
                        'status' => 'approved',

                        'approved_version_id' => $version->id,

                        'approved_at' => now(),
                    ]);
                } else {
                    $version->update([
                        'status' => 'revision_required',
                    ]);

                    $requirement->update([
                        'status' => 'revision_required',
                    ]);
                }
            }
        );

        if (
            $decision
            === 'revision_required'
            && $requirement->owner
        ) {
            $requirement->owner->notify(
                new RequirementRevisionRequiredNotification(
                    $requirement
                )
            );
        }

        return back()->with(
            'success',
            $decision === 'approved'
                ? 'Requirement approved.'
                : 'Revision requirement recorded.'
        );
    }

    private function assertBelongs(
        Requirement $requirement,
        RequirementVersion $version
    ): void {
        abort_unless(
            $version->requirement_id
                === $requirement->id,
            404
        );
    }
}
