<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EstimateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEstimateRequest;
use App\Models\CompanySetting;
use App\Models\Estimate;
use App\Models\Requirement;
use App\Models\RequirementVersion;
use App\Services\DocumentNumberService;
use App\Services\Estimation\EstimateCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class EstimateController extends Controller
{
    public function create(
        Requirement $requirement,
        RequirementVersion $version
    ): Response {
        abort_unless(
            request()->user()->can(
                'estimates.create'
            ),
            403
        );

        $this->assertVersion(
            $requirement,
            $version
        );

        return Inertia::render(
            'admin/estimates/edit',
            [
                'requirement' => $requirement->load(
                    'client'
                ),

                'version' => $version,

                'estimate' => null,
            ]
        );
    }

    public function store(
        StoreEstimateRequest $request,
        Requirement $requirement,
        RequirementVersion $version,
        DocumentNumberService $numbers,
        EstimateCalculatorService $calculator
    ): RedirectResponse {
        $this->assertVersion(
            $requirement,
            $version
        );

        $validated =
            $request->validated();

        $estimate =
            DB::transaction(
                function () use (
                    $request,
                    $version,
                    $validated,
                    $numbers
                ): Estimate {
                    $estimate =
                        Estimate::create([
                            'estimate_number' => $numbers->next(
                                'estimate'
                            ),

                            'requirement_version_id' => $version->id,

                            'status' => 'draft',

                            'currency' => strtoupper(
                                $validated[
                                    'currency'
                                ]
                            ),

                            'contingency_percent' => $validated[
                                    'contingency_percent'
                                ],

                            'assumptions' => $validated[
                                    'assumptions'
                                ] ?? null,

                            'internal_notes' => $validated[
                                    'internal_notes'
                                ] ?? null,

                            'created_by' => $request->user()->id,
                        ]);

                    $this->replaceLines(
                        $estimate,
                        $validated['lines']
                    );

                    return $estimate;
                }
            );

        $calculator->recalculate(
            $estimate
        );

        return redirect()
            ->route(
                'admin.estimates.edit',
                $estimate
            )
            ->with(
                'success',
                'Estimate created.'
            );
    }

    public function edit(
        Estimate $estimate
    ): Response {
        Gate::authorize(
            'view',
            $estimate
        );

        $estimate->load([
            'lines',
            'requirementVersion.requirement.client',
        ]);

        return Inertia::render(
            'admin/estimates/edit',
            [
                'estimate' => $estimate,

                'version' => $estimate
                    ->requirementVersion,

                'requirement' => $estimate
                    ->requirementVersion
                    ->requirement,
            ]
        );
    }

    public function update(
        StoreEstimateRequest $request,
        Estimate $estimate,
        EstimateCalculatorService $calculator
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $estimate
        );

        $validated =
            $request->validated();

        DB::transaction(
            function () use (
                $estimate,
                $validated
            ): void {
                $estimate->update([
                    'currency' => strtoupper(
                        $validated[
                            'currency'
                        ]
                    ),

                    'contingency_percent' => $validated[
                            'contingency_percent'
                        ],

                    'assumptions' => $validated[
                            'assumptions'
                        ] ?? null,

                    'internal_notes' => $validated[
                            'internal_notes'
                        ] ?? null,
                ]);

                $estimate
                    ->lines()
                    ->delete();

                $this->replaceLines(
                    $estimate,
                    $validated[
                        'lines'
                    ]
                );
            }
        );

        $calculator->recalculate(
            $estimate
        );

        return back()->with(
            'success',
            'Estimate updated.'
        );
    }

    public function approve(
        Request $request,
        Estimate $estimate
    ): RedirectResponse {
        Gate::authorize(
            'approve',
            $estimate
        );

        $estimate->update([
            'status' => 'approved',

            'approved_by' => $request->user()->id,

            'approved_at' => now(),
        ]);

        return back()->with(
            'success',
            'Estimate approved.'
        );
    }

    public function excel(
        Request $request,
        Estimate $estimate
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'estimates.export'
            ),
            403
        );

        return Excel::download(
            new EstimateExport(
                $estimate
            ),
            "{$estimate->estimate_number}.xlsx"
        );
    }

    public function pdf(
        Request $request,
        Estimate $estimate
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'estimates.export'
            ),
            403
        );

        $estimate->load([
            'lines',
            'requirementVersion.requirement.client',
        ]);

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $estimate
            )
            ->event('export')
            ->log(
                'Estimate PDF generated'
            );

        return Pdf::loadView(
            'pdf.estimate',
            [
                'estimate' => $estimate,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )
            ->setPaper(
                'a4',
                'landscape'
            )
            ->download(
                "{$estimate->estimate_number}.pdf"
            );
    }

    private function replaceLines(
        Estimate $estimate,
        array $lines
    ): void {
        foreach (
            array_values($lines) as $index => $line
        ) {
            $estimate
                ->lines()
                ->create([
                    'sort_order' => $index + 1,

                    'module_name' => $line[
                            'module_name'
                        ],

                    'description' => $line[
                            'description'
                        ] ?? null,

                    'estimated_hours' => $line[
                            'estimated_hours'
                        ],

                    'billing_rate' => $line[
                            'billing_rate'
                        ],

                    'cost_rate' => $line[
                            'cost_rate'
                        ],
                ]);
        }
    }

    private function assertVersion(
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
