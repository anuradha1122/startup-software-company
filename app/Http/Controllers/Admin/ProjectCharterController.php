<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectCharterRequest;
use App\Models\Project;
use App\Models\ProjectCharter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class ProjectCharterController extends Controller
{
    public function edit(
        Project $project,
        ProjectCharter $charter
    ): Response {
        abort_unless(
            request()->user()->can(
                'projects.charter.manage'
            ),
            403
        );

        $this->assertBelongs(
            $project,
            $charter
        );

        abort_unless(
            $charter->isEditable(),
            409
        );

        return Inertia::render(
            'admin/projects/charter-edit',
            [
                'project' => $project->load([
                    'client',
                    'contract',
                ]),

                'charter' => $charter,
            ]
        );
    }

    public function update(
        StoreProjectCharterRequest $request,
        Project $project,
        ProjectCharter $charter
    ): RedirectResponse {
        $this->assertBelongs(
            $project,
            $charter
        );

        abort_unless(
            $charter->isEditable(),
            409
        );

        $data =
            $request->validated();

        foreach ([
            'success_criteria',
            'assumptions',
            'constraints',
        ] as $field) {
            $data[$field] =
                $this->cleanArray(
                    $data[$field]
                    ?? []
                );
        }

        $charter->update($data);

        return redirect()
            ->route(
                'admin.projects.show',
                $project
            )
            ->with(
                'success',
                'Project charter updated.'
            );
    }

    public function approve(
        Request $request,
        Project $project,
        ProjectCharter $charter
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'projects.charter.approve'
            ),
            403
        );

        $this->assertBelongs(
            $project,
            $charter
        );

        abort_unless(
            $charter->status
                === 'draft',
            409
        );

        DB::transaction(
            function () use (
                $request,
                $project,
                $charter
            ): void {
                $project
                    ->charters()
                    ->where(
                        'id',
                        '!=',
                        $charter->id
                    )
                    ->where(
                        'status',
                        'approved'
                    )
                    ->update([
                        'status' => 'superseded',
                    ]);

                $charter->update([
                    'status' => 'approved',

                    'approved_by' => $request->user()->id,

                    'approved_at' => now(),
                ]);

                $project->update([
                    'approved_charter_id' => $charter->id,
                ]);
            }
        );

        return back()->with(
            'success',
            'Project charter approved.'
        );
    }

    public function revision(
        Request $request,
        Project $project
    ): RedirectResponse {
        abort_unless(
            $request->user()->can(
                'projects.charter.manage'
            ),
            403
        );

        $latest =
            $project
                ->charters()
                ->orderByDesc(
                    'version_number'
                )
                ->firstOrFail();

        if ($latest->status === 'draft') {
            throw new RuntimeException(
                'A draft charter already exists.'
            );
        }

        $charter =
            $project
                ->charters()
                ->create([
                    'version_number' => $latest->version_number
                        + 1,

                    'status' => 'draft',

                    'business_objective' => $latest
                        ->business_objective,

                    'project_objective' => $latest
                        ->project_objective,

                    'governance_structure' => $latest
                        ->governance_structure,

                    'communication_plan' => $latest
                        ->communication_plan,

                    'escalation_process' => $latest
                        ->escalation_process,

                    'success_criteria' => $latest
                        ->success_criteria,

                    'assumptions' => $latest
                        ->assumptions,

                    'constraints' => $latest
                        ->constraints,

                    'created_by' => $request->user()->id,
                ]);

        return redirect()
            ->route(
                'admin.projects.charters.edit',
                [
                    $project,
                    $charter,
                ]
            );
    }

    private function assertBelongs(
        Project $project,
        ProjectCharter $charter
    ): void {
        abort_unless(
            $charter->project_id
                === $project->id,
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
