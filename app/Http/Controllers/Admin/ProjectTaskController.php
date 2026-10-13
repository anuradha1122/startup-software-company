<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProjectTasksExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectTaskRequest;
use App\Http\Requests\Admin\UpdateProjectTaskRequest;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\TaskWorkflowStatus;
use App\Notifications\TaskAssignedNotification;
use App\Services\People\AssignmentEligibilityService;
use App\Services\Tasks\TaskNumberService;
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

class ProjectTaskController extends Controller
{
    public function board(
        Project $project
    ): Response {
        abort_unless(
            request()->user()->can(
                'tasks.view'
            ),
            403
        );

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
            'Tasks are available only for active or on-hold projects.'
        );

        $statuses =
            TaskWorkflowStatus::query()
                ->where(
                    'active',
                    true
                )
                ->orderBy(
                    'sort_order'
                )
                ->get();

        $tasks =
            ProjectTask::query()
                ->where(
                    'project_id',
                    $project->id
                )
                ->with([
                    'workflowStatus',
                    'assignee',
                    'phase',
                    'milestone',
                ])
                ->orderBy(
                    'sort_order'
                )
                ->get()
                ->groupBy(
                    'workflow_status_id'
                );

        return Inertia::render(
            'admin/projects/tasks/board',
            [
                'project' => $project->load([
                    'client',
                    'phases',
                    'milestones',
                ]),

                'statuses' => $statuses,

                'tasksByStatus' => $tasks,

                'users' => $project
                    ->assignments()
                    ->where(
                        'status',
                        'active'
                    )
                    ->where(function ($query): void {
                        $query
                            ->whereNull(
                                'start_date'
                            )
                            ->orWhereDate(
                                'start_date',
                                '<=',
                                today()
                            );
                    })
                    ->where(function ($query): void {
                        $query
                            ->whereNull(
                                'end_date'
                            )
                            ->orWhereDate(
                                'end_date',
                                '>=',
                                today()
                            );
                    })
                    ->with('user')
                    ->get()
                    ->pluck('user')
                    ->filter()
                    ->values(),
            ]
        );
    }

    public function store(
        StoreProjectTaskRequest $request,
        Project $project,
        TaskNumberService $numbers,
        AssignmentEligibilityService $eligibility
    ): RedirectResponse {
        abort_unless(
            $project->status
            === 'active',
            409,
            'New tasks may only be created in an active project.'
        );

        $data =
            $request->validated();

        $eligibility->assertAssignable(
            $project,
            isset($data['assignee_id'])
                ? (int) $data['assignee_id']
                : null
        );

        $this->validateRelations(
            $project,
            $data
        );

        $initialStatus =
            TaskWorkflowStatus::query()
                ->where(
                    'is_initial',
                    true
                )
                ->where(
                    'active',
                    true
                )
                ->firstOrFail();

        $number =
            $numbers->next(
                $project
            );

        $task =
            DB::transaction(
                function () use (
                    $request,
                    $project,
                    $data,
                    $initialStatus,
                    $number
                ): ProjectTask {
                    $dependencyIds =
                        $data[
                            'dependency_ids'
                        ] ?? [];

                    unset(
                        $data[
                            'dependency_ids'
                        ]
                    );

                    $task =
                        $project
                            ->tasks()
                            ->create([
                                ...$data,

                                'task_sequence' => $number[
                                        'sequence'
                                    ],

                                'task_number' => $number[
                                        'number'
                                    ],

                                'workflow_status_id' => $initialStatus->id,

                                'created_by' => $request
                                    ->user()
                                    ->id,
                            ]);

                    $task
                        ->dependencies()
                        ->sync(
                            $dependencyIds
                        );

                    $task
                        ->transitions()
                        ->create([
                            'from_status_id' => null,

                            'to_status_id' => $initialStatus
                                ->id,

                            'changed_by' => $request
                                ->user()
                                ->id,

                            'notes' => 'Task created.',

                            'changed_at' => now(),
                        ]);

                    return $task;
                }
            );

        if ($task->assignee) {
            $task->assignee->notify(
                new TaskAssignedNotification(
                    $task->load('project')
                )
            );
        }

        return redirect()
            ->route(
                'admin.tasks.show',
                $task
            )
            ->with(
                'success',
                'Task created.'
            );
    }

    public function show(
        ProjectTask $task
    ): Response {
        Gate::authorize(
            'view',
            $task
        );

        $task->load([
            'project.phases',
            'project.milestones',
            'workflowStatus',
            'assignee',
            'parent',
            'children',
            'dependencies.workflowStatus',
            'comments.creator',

            'submissions' => fn ($query) => $query
                ->with([
                    'submitter',
                    'reviewer',
                    'attachments',
                ])
                ->orderByDesc(
                    'version_number'
                ),

            'transitions.fromStatus',
            'transitions.toStatus',
            'transitions.changedBy',
        ]);

        return Inertia::render(
            'admin/tasks/show',
            [
                'task' => $task,

                'statuses' => TaskWorkflowStatus::query()
                    ->where(
                        'active',
                        true
                    )
                    ->orderBy(
                        'sort_order'
                    )
                    ->get(),

                'users' => $project
                    ->assignments()
                    ->where(
                        'status',
                        'active'
                    )
                    ->where(function ($query): void {
                        $query
                            ->whereNull(
                                'start_date'
                            )
                            ->orWhereDate(
                                'start_date',
                                '<=',
                                today()
                            );
                    })
                    ->where(function ($query): void {
                        $query
                            ->whereNull(
                                'end_date'
                            )
                            ->orWhereDate(
                                'end_date',
                                '>=',
                                today()
                            );
                    })
                    ->with('user')
                    ->get()
                    ->pluck('user')
                    ->filter()
                    ->values(),

                'availableDependencies' => ProjectTask::query()
                    ->where(
                        'project_id',
                        $task->project_id
                    )
                    ->where(
                        'id',
                        '!=',
                        $task->id
                    )
                    ->orderBy(
                        'task_sequence'
                    )
                    ->get([
                        'id',
                        'public_id',
                        'task_number',
                        'title',
                    ]),
            ]
        );
    }

    public function update(
        UpdateProjectTaskRequest $request,
        ProjectTask $task,
        AssignmentEligibilityService $eligibility
    ): RedirectResponse {
        Gate::authorize(
            'update',
            $task
        );

        $data =
            $request->validated();

        $eligibility->assertAssignable(
            $task->project,
            isset($data['assignee_id'])
                ? (int) $data['assignee_id']
                : null
        );

        $this->validateRelations(
            $task->project,
            $data,
            $task
        );

        $dependencyIds =
            $data[
                'dependency_ids'
            ] ?? [];

        unset(
            $data[
                'dependency_ids'
            ]
        );

        $oldAssignee =
            $task->assignee_id;

        DB::transaction(
            function () use (
                $task,
                $data,
                $dependencyIds
            ): void {
                $task->update($data);

                $task
                    ->dependencies()
                    ->sync(
                        $dependencyIds
                    );
            }
        );

        if (
            $task->assignee_id
            && $task->assignee_id
            !== $oldAssignee
        ) {
            $task
                ->assignee
                ->notify(
                    new TaskAssignedNotification(
                        $task->load(
                            'project'
                        )
                    )
                );
        }

        return back()->with(
            'success',
            'Task updated.'
        );
    }

    public function destroy(
        ProjectTask $task
    ): RedirectResponse {
        Gate::authorize(
            'delete',
            $task
        );

        $project =
            $task->project;

        $task->delete();

        return redirect()
            ->route(
                'admin.projects.tasks.board',
                $project
            )
            ->with(
                'success',
                'Task deleted.'
            );
    }

    public function excel(
        Request $request,
        Project $project
    ): BinaryFileResponse {
        abort_unless(
            $request->user()->can(
                'tasks.export'
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
                'Project tasks Excel generated'
            );

        return Excel::download(
            new ProjectTasksExport(
                $project
            ),
            "{$project->project_number}-tasks.xlsx"
        );
    }

    public function pdf(
        Request $request,
        Project $project
    ): HttpResponse {
        abort_unless(
            $request->user()->can(
                'tasks.export'
            ),
            403
        );

        $project->load([
            'client',

            'tasks' => fn ($query) => $query
                ->with([
                    'workflowStatus',
                    'assignee',
                    'phase',
                    'milestone',
                ])
                ->orderBy(
                    'task_sequence'
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
                'Project task report PDF generated'
            );

        return Pdf::loadView(
            'pdf.project-tasks',
            [
                'project' => $project,

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
                "{$project->project_number}-tasks.pdf"
            );
    }

    private function validateRelations(
        Project $project,
        array $data,
        ?ProjectTask $task = null
    ): void {
        if (
            ! empty(
                $data[
                    'project_phase_id'
                ]
            )
        ) {
            abort_unless(
                $project
                    ->phases()
                    ->whereKey(
                        $data[
                            'project_phase_id'
                        ]
                    )
                    ->exists(),
                422,
                'The selected phase does not belong to this project.'
            );
        }

        if (
            ! empty(
                $data[
                    'project_milestone_id'
                ]
            )
        ) {
            abort_unless(
                $project
                    ->milestones()
                    ->whereKey(
                        $data[
                            'project_milestone_id'
                        ]
                    )
                    ->exists(),
                422,
                'The selected milestone does not belong to this project.'
            );
        }

        if (
            ! empty(
                $data[
                    'parent_task_id'
                ]
            )
        ) {
            abort_unless(
                $project
                    ->tasks()
                    ->whereKey(
                        $data[
                            'parent_task_id'
                        ]
                    )
                    ->exists(),
                422
            );

            abort_if(
                $task
                    && $task->id
                    === (int) $data[
                        'parent_task_id'
                    ],
                422,
                'A task cannot be its own parent.'
            );
        }

        $dependencyIds =
            $data[
                'dependency_ids'
            ] ?? [];

        if ($task) {
            abort_if(
                in_array(
                    $task->id,
                    array_map(
                        'intval',
                        $dependencyIds
                    ),
                    true
                ),
                422,
                'A task cannot depend on itself.'
            );
        }

        if ($dependencyIds) {
            $validCount =
                $project
                    ->tasks()
                    ->whereIn(
                        'id',
                        $dependencyIds
                    )
                    ->count();

            abort_unless(
                $validCount
                === count(
                    array_unique(
                        $dependencyIds
                    )
                ),
                422,
                'Dependencies must belong to this project.'
            );
        }
    }
}
