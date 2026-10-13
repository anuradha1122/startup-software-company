import { Head, Link, router, useForm } from '@inertiajs/react';

type TaskForm = {
    parent_task_id: string;
    project_phase_id: string;
    project_milestone_id: string;
    title: string;
    description: string;
    type: 'task' | 'bug' | 'change' | 'research';
    priority: 'low' | 'medium' | 'high' | 'urgent';
    assignee_id: string;
    estimated_hours: number | '';
    planned_start_date: string;
    due_date: string;
    client_visible: boolean;
    dependency_ids: number[];
};

export default function TaskBoard({
    project,
    statuses,
    tasksByStatus,
    users,
}: any) {
    const form = useForm<TaskForm>({
        parent_task_id: '',
        project_phase_id: '',
        project_milestone_id: '',
        title: '',
        description: '',
        type: 'task',
        priority: 'medium',
        assignee_id: '',
        estimated_hours: '',
        planned_start_date: '',
        due_date: '',
        client_visible: false,
        dependency_ids: [],
    });

    const submit = () => {
        form.post(`/admin/projects/${project.public_id}/tasks`, {
            preserveScroll: true,

            onSuccess: () => form.reset(),
        });
    };

    return (
        <>
            <Head title={`${project.project_number} Tasks`} />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {project.project_number}
                            {' — '}
                            Task Board
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            {project.name}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/projects/${project.public_id}/tasks/export/excel`}
                            className="rounded border px-3 py-2"
                        >
                            Excel
                        </a>

                        <a
                            href={`/admin/projects/${project.public_id}/tasks/export/pdf`}
                            className="rounded border px-3 py-2"
                        >
                            PDF
                        </a>

                        <Link
                            href={`/admin/projects/${project.public_id}`}
                            className="rounded border px-3 py-2"
                        >
                            Project
                        </Link>
                    </div>
                </div>

                {project.status === 'active' && (
                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">New Task</h2>

                        <div className="grid gap-3 md:grid-cols-3">
                            <input
                                className="rounded border p-2 md:col-span-2"
                                placeholder="Task title"
                                value={form.data.title}
                                onChange={(e) =>
                                    form.setData('title', e.target.value)
                                }
                            />

                            <select
                                className="rounded border p-2"
                                value={form.data.assignee_id}
                                onChange={(e) =>
                                    form.setData('assignee_id', e.target.value)
                                }
                            >
                                <option value="">Unassigned</option>

                                {users.map((user: any) => (
                                    <option key={user.id} value={user.id}>
                                        {user.name}
                                    </option>
                                ))}
                            </select>

                            <select
                                className="rounded border p-2"
                                value={form.data.type}
                                onChange={(e) =>
                                    form.setData(
                                        'type',
                                        e.target.value as TaskForm['type'],
                                    )
                                }
                            >
                                <option value="task">Task</option>
                                <option value="bug">Bug</option>
                                <option value="change">Change</option>
                                <option value="research">Research</option>
                            </select>

                            <select
                                className="rounded border p-2"
                                value={form.data.priority}
                                onChange={(e) =>
                                    form.setData(
                                        'priority',
                                        e.target.value as TaskForm['priority'],
                                    )
                                }
                            >
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>

                            <select
                                className="rounded border p-2"
                                value={form.data.project_phase_id}
                                onChange={(e) =>
                                    form.setData(
                                        'project_phase_id',
                                        e.target.value,
                                    )
                                }
                            >
                                <option value="">No phase</option>

                                {project.phases.map((phase: any) => (
                                    <option key={phase.id} value={phase.id}>
                                        {phase.name}
                                    </option>
                                ))}
                            </select>

                            <select
                                className="rounded border p-2"
                                value={form.data.project_milestone_id}
                                onChange={(e) =>
                                    form.setData(
                                        'project_milestone_id',
                                        e.target.value,
                                    )
                                }
                            >
                                <option value="">No milestone</option>

                                {project.milestones.map((milestone: any) => (
                                    <option
                                        key={milestone.id}
                                        value={milestone.id}
                                    >
                                        {milestone.name}
                                    </option>
                                ))}
                            </select>

                            <input
                                type="number"
                                min="0"
                                step="0.25"
                                className="rounded border p-2"
                                placeholder="Estimated hours"
                                value={form.data.estimated_hours}
                                onChange={(e) =>
                                    form.setData(
                                        'estimated_hours',
                                        e.target.value
                                            ? Number(e.target.value)
                                            : '',
                                    )
                                }
                            />

                            <input
                                type="date"
                                className="rounded border p-2"
                                value={form.data.planned_start_date}
                                onChange={(e) =>
                                    form.setData(
                                        'planned_start_date',
                                        e.target.value,
                                    )
                                }
                            />

                            <input
                                type="date"
                                className="rounded border p-2"
                                value={form.data.due_date}
                                onChange={(e) =>
                                    form.setData('due_date', e.target.value)
                                }
                            />

                            <textarea
                                className="min-h-24 rounded border p-2 md:col-span-3"
                                placeholder="Description"
                                value={form.data.description}
                                onChange={(e) =>
                                    form.setData('description', e.target.value)
                                }
                            />

                            <button
                                type="button"
                                disabled={form.processing}
                                onClick={submit}
                                className="rounded bg-primary px-4 py-2 text-primary-foreground"
                            >
                                Create Task
                            </button>
                        </div>
                    </section>
                )}

                <div className="overflow-x-auto">
                    <div className="flex min-w-max gap-4 pb-4">
                        {statuses.map((status: any) => {
                            const tasks = tasksByStatus[status.id] ?? [];

                            return (
                                <section
                                    key={status.public_id}
                                    className="w-80 shrink-0 rounded border bg-muted/20"
                                >
                                    <div className="border-b p-3">
                                        <div className="flex justify-between">
                                            <h2 className="font-semibold">
                                                {status.name}
                                            </h2>

                                            <span className="text-sm text-muted-foreground">
                                                {tasks.length}
                                            </span>
                                        </div>
                                    </div>

                                    <div className="space-y-3 p-3">
                                        {tasks.map((task: any) => (
                                            <div
                                                key={task.public_id}
                                                className="rounded border bg-background p-3"
                                            >
                                                <Link
                                                    href={`/admin/tasks/${task.public_id}`}
                                                    className="font-medium hover:underline"
                                                >
                                                    {task.task_number}
                                                </Link>

                                                <p className="mt-1">
                                                    {task.title}
                                                </p>

                                                <div className="mt-2 text-xs text-muted-foreground">
                                                    {task.priority}
                                                    {' · '}
                                                    {task.assignee?.name ??
                                                        'Unassigned'}
                                                </div>

                                                <select
                                                    className="mt-3 w-full rounded border p-1 text-sm"
                                                    value={status.id}
                                                    onChange={(e) =>
                                                        router.post(
                                                            `/admin/tasks/${task.public_id}/move`,
                                                            {
                                                                workflow_status_id:
                                                                    Number(
                                                                        e.target
                                                                            .value,
                                                                    ),
                                                            },
                                                            {
                                                                preserveScroll: true,
                                                            },
                                                        )
                                                    }
                                                >
                                                    {statuses.map(
                                                        (option: any) => (
                                                            <option
                                                                key={option.id}
                                                                value={
                                                                    option.id
                                                                }
                                                            >
                                                                {option.name}
                                                            </option>
                                                        ),
                                                    )}
                                                </select>
                                            </div>
                                        ))}
                                    </div>
                                </section>
                            );
                        })}
                    </div>
                </div>
            </div>
        </>
    );
}
