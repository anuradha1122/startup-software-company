<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectExpensesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectExpenseRequest;
use App\Http\Requests\Admin\UpdateProjectExpenseRequest;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\Supplier;
use App\Models\User;
use App\Services\DocumentNumberService;
use App\Services\Expenses\ExpenseCalculatorService;
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

class ProjectExpenseController extends Controller
{
    public function index(
        Request $request,
        Project $project
    ): Response {
        abort_unless(
            $request->user()->can(
                'expenses.view-all'
            )
            || $request->user()->can(
                'expenses.view-own'
            ),
            403
        );

        $query =
            $project
                ->expenses()
                ->with([
                    'supplier',
                    'incurredBy',
                    'creator',
                ])
                ->latest(
                    'expense_date'
                );

        if (
            ! $request->user()->can(
                'expenses.view-all'
            )
        ) {
            $query->where(
                function ($inner) use (
                    $request
                ): void {
                    $inner
                        ->where(
                            'created_by',
                            $request
                                ->user()
                                ->id
                        )
                        ->orWhere(
                            'incurred_by',
                            $request
                                ->user()
                                ->id
                        );
                }
            );
        }

        return Inertia::render(
            'admin/projects/expenses/index',
            [
                'project' => $project->load(
                    'client'
                ),

                'expenses' => $query
                    ->paginate(25),

                'suppliers' => Supplier::query()
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderBy(
                        'name'
                    )
                    ->get(),

                'users' => User::query()
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderBy(
                        'name'
                    )
                    ->get([
                        'id',
                        'name',
                    ]),
            ]
        );
    }

    public function store(
        StoreProjectExpenseRequest $request,
        Project $project,
        DocumentNumberService $numbers,
        ExpenseCalculatorService $calculator
    ): RedirectResponse {
        abort_unless(
            in_array(
                $project->status,
                [
                    'active',
                    'on_hold',
                ],
                true
            ),
            409,
            'Expenses may only be recorded for active or on-hold projects.'
        );

        $data =
            $request->validated();

        $totals =
            $calculator->calculate(
                (float) $data[
                    'subtotal'
                ],
                (float) (
                    $data[
                        'tax_amount'
                    ] ?? 0
                )
            );

        $files =
            $request->file(
                'attachments',
                []
            );

        unset(
            $data['attachments']
        );

        $expense =
            DB::transaction(
                function () use (
                    $request,
                    $project,
                    $numbers,
                    $data,
                    $totals
                ): ProjectExpense {
                    return $project
                        ->expenses()
                        ->create([
                            ...$data,
                            ...$totals,

                            'expense_number' => $numbers->next(
                                'expense'
                            ),

                            'currency' => strtoupper(
                                $data['currency']
                            ),

                            'status' => 'draft',

                            'incurred_by' => $data['incurred_by']
                                ?? $request
                                    ->user()
                                    ->id,

                            'created_by' => $request
                                ->user()
                                ->id,
                        ]);
                }
            );

        foreach (
            $files as $file
        ) {
            $path =
                $file->store(
                    "project-expenses/{$expense->public_id}",
                    'local'
                );

            $expense
                ->attachments()
                ->create([
                    'type' => 'receipt',

                    'title' => $file
                        ->getClientOriginalName(),

                    'original_name' => $file
                        ->getClientOriginalName(),

                    'disk' => 'local',

                    'path' => $path,

                    'mime_type' => $file
                        ->getMimeType(),

                    'size' => $file
                        ->getSize(),

                    'uploaded_by' => $request
                        ->user()
                        ->id,
                ]);
        }

        return redirect()
            ->route(
                'admin.expenses.show',
                $expense
            )
            ->with(
                'success',
                'Expense created.'
            );
    }

    public function show(
        ProjectExpense $expense
    ): Response {
        Gate::authorize(
            'view',
            $expense
        );

        $expense->load([
            'project.client',
            'supplier',
            'incurredBy',
            'creator',
            'reviewer',
            'attachments',
        ]);

        return Inertia::render(
            'admin/projects/expenses/show',
            [
                'expense' => $expense,

                'suppliers' => Supplier::query()
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderBy(
                        'name'
                    )
                    ->get(),
            ]
        );
    }

    public function update(
        UpdateProjectExpenseRequest $request,
        ProjectExpense $expense,
        ExpenseCalculatorService $calculator
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $expense
        );

        $data =
            $request->validated();

        unset(
            $data['attachments']
        );

        $totals =
            $calculator->calculate(
                (float) $data[
                    'subtotal'
                ],
                (float) (
                    $data[
                        'tax_amount'
                    ] ?? 0
                )
            );

        $expense->update([
            ...$data,
            ...$totals,

            'currency' => strtoupper(
                $data['currency']
            ),
        ]);

        return back()->with(
            'success',
            'Expense updated.'
        );
    }

    public function destroy(
        ProjectExpense $expense
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $expense
        );

        $project =
            $expense->project;

        $expense->delete();

        return redirect()
            ->route(
                'admin.projects.expenses.index',
                $project
            )
            ->with(
                'success',
                'Expense deleted.'
            );
    }

    public function excel(
        Request $request,
        Project $project
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'expenses.export'
            ),
            403
        );

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $project
            )
            ->event('export')
            ->log(
                'Project expense Excel generated'
            );

        return Excel::download(
            new ProjectExpensesExport(
                $project
            ),
            "{$project->project_number}-expenses.xlsx"
        );
    }

    public function pdf(
        Request $request,
        Project $project
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'expenses.export'
            ),
            403
        );

        $project->load([
            'client',

            'expenses' => fn ($query) => $query
                ->with([
                    'supplier',
                    'incurredBy',
                ])
                ->orderBy(
                    'expense_date'
                ),
        ]);

        activity('exports')
            ->causedBy(
                $request->user()
            )
            ->performedOn(
                $project
            )
            ->event('export')
            ->log(
                'Project expense PDF generated'
            );

        return Pdf::loadView(
            'pdf.project-expenses',
            [
                'project' => $project,

                'company' => CompanySetting::first(),

                'generatedBy' => $request->user(),

                'generatedAt' => now(),
            ]
        )->download(
            "{$project->project_number}-expenses.pdf"
        );
    }
}
