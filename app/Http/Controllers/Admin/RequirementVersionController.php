<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRequirementVersionRequest;
use App\Models\Requirement;
use App\Models\RequirementVersion;
use App\Models\User;
use App\Notifications\RequirementReviewRequestedNotification;
use App\Services\Requirements\RequirementVersionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class RequirementVersionController extends Controller
{
    public function edit(
        Requirement $requirement,
        RequirementVersion $version
    ): Response {
        abort_unless(
            request()->user()->can(
                'requirements.update'
            ),
            403
        );

        $this->assertBelongs(
            $requirement,
            $version
        );

        abort_unless(
            $version->isEditable(),
            409,
            'Only draft requirement versions can be edited.'
        );

        return Inertia::render(
            'admin/requirements/edit-version',
            [
                'requirement' => $requirement->load(
                    'client'
                ),

                'version' => $version,
            ]
        );
    }

    public function update(
        UpdateRequirementVersionRequest $request,
        Requirement $requirement,
        RequirementVersion $version
    ): RedirectResponse {
        $this->assertBelongs(
            $requirement,
            $version
        );

        abort_unless(
            $version->isEditable(),
            409,
            'Approved or submitted versions are locked.'
        );

        $validated =
            $request->validated();

        foreach ([
            'functional_requirements',
            'non_functional_requirements',
            'assumptions',
            'constraints',
            'acceptance_criteria',
        ] as $field) {
            $validated[$field] =
                $this->cleanArray(
                    $validated[$field]
                    ?? []
                );
        }

        $version->update(
            $validated
        );

        return redirect()
            ->route(
                'admin.requirements.show',
                $requirement
            )
            ->with(
                'success',
                'Requirement version updated.'
            );
    }

    public function createRevision(
        Request $request,
        Requirement $requirement,
        RequirementVersionService $service
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'requirements.update'
            ),
            403
        );

        $version =
            $service->createRevision(
                $requirement,
                $request->user()->id
            );

        $requirement->update([
            'status' => 'draft',
        ]);

        return redirect()
            ->route(
                'admin.requirements.versions.edit',
                [
                    $requirement,
                    $version,
                ]
            )
            ->with(
                'success',
                'New requirement revision created.'
            );
    }

    public function submitReview(
        Request $request,
        Requirement $requirement,
        RequirementVersion $version
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'requirements.review'
            ),
            403
        );

        $this->assertBelongs(
            $requirement,
            $version
        );

        abort_unless(
            $version->status
                === 'draft',
            409
        );

        $version->update([
            'status' => 'in_review',

            'submitted_for_review_at' => now(),
        ]);

        $requirement->update([
            'status' => 'in_review',
        ]);

        $recipients =
            User::role([
                'Director',
                'Admin',
                'Project Manager',
            ])
                ->where(
                    'status',
                    'active'
                )
                ->get()
                ->reject(
                    fn (User $user) => $user->id
                        === $request->user()->id
                );

        Notification::send(
            $recipients,
            new RequirementReviewRequestedNotification(
                $requirement
            )
        );

        return back()->with(
            'success',
            'Requirement submitted for review.'
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

    private function cleanArray(
        array $values
    ): array {
        return array_values(
            array_filter(
                array_map(
                    fn ($value) => trim(
                        (string) $value
                    ),
                    $values
                ),
                fn ($value) => $value !== ''
            )
        );
    }
}
