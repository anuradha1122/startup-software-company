import { Head, router, useForm } from '@inertiajs/react';

type EntryForm = {
    project_id: string;
    project_task_id: string;
    work_date: string;
    hours: number | '';
    description: string;
    billable: boolean;
};

type ReviewForm = {
    decision: 'approved' | 'rejected';
    review_notes: string;
};

export default function TimesheetShow({ timesheet, projects, canReview }: any) {
    const entryForm = useForm<EntryForm>({
        project_id: '',
        project_task_id: '',
        work_date: timesheet.week_start,
        hours: '',
        description: '',
        billable: true,
    });

    const reviewForm = useForm<ReviewForm>({
        decision: 'approved',
        review_notes: '',
    });

    const selectedProject = projects.find(
        (project: any) => String(project.id) === entryForm.data.project_id,
    );

    const editable = ['draft', 'rejected'].includes(timesheet.status);

    const totalHours = timesheet.entries.reduce(
        (total: number, entry: any) => total + Number(entry.hours),
        0,
    );

    return (
        <>
            <Head title="Timesheet" />

            <div className="max-w-7xl space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Weekly Timesheet
                        </h1>

                        <p>{timesheet.user.name}</p>

                        <p className="text-sm text-muted-foreground">
                            {timesheet.week_start}
                            {' → '}
                            {timesheet.week_end}
                            {' · '}
                            {timesheet.status}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/timesheets/${timesheet.public_id}/pdf`}
                            className="rounded border px-3 py-2"
                        >
                            PDF
                        </a>

                        <a
                            href={`/admin/timesheets/${timesheet.public_id}/excel`}
                            className="rounded border px-3 py-2"
                        >
                            Excel
                        </a>

                        {editable && timesheet.entries.length > 0 && (
                            <button
                                onClick={() =>
                                    router.post(
                                        `/admin/timesheets/${timesheet.public_id}/submit`,
                                    )
                                }
                                className="rounded bg-primary px-3 py-2 text-primary-foreground"
                            >
                                Submit
                            </button>
                        )}
                    </div>
                </div>

                <section className="grid gap-4 rounded border p-4 md:grid-cols-3">
                    <div>
                        <span className="text-sm text-muted-foreground">
                            Week
                        </span>

                        <p>
                            {timesheet.week_start}
                            {' → '}
                            {timesheet.week_end}
                        </p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Status
                        </span>

                        <p>{timesheet.status}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Total Hours
                        </span>

                        <p>{totalHours.toFixed(2)}</p>
                    </div>
                </section>

                {editable && (
                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">
                            Add Time Entry
                        </h2>

                        <form
                            className="grid gap-3 md:grid-cols-3"
                            onSubmit={(event) => {
                                event.preventDefault();

                                entryForm.post(
                                    `/admin/timesheets/${timesheet.public_id}/entries`,
                                    {
                                        preserveScroll: true,

                                        onSuccess: () => entryForm.reset(),
                                    },
                                );
                            }}
                        >
                            <select
                                className="rounded border p-2"
                                value={entryForm.data.project_id}
                                onChange={(e) => {
                                    entryForm.setData(
                                        'project_id',
                                        e.target.value,
                                    );

                                    entryForm.setData('project_task_id', '');
                                }}
                            >
                                <option value="">Project</option>

                                {projects.map((project: any) => (
                                    <option key={project.id} value={project.id}>
                                        {project.project_number}
                                        {' — '}
                                        {project.name}
                                    </option>
                                ))}
                            </select>

                            <select
                                className="rounded border p-2"
                                value={entryForm.data.project_task_id}
                                onChange={(e) =>
                                    entryForm.setData(
                                        'project_task_id',
                                        e.target.value,
                                    )
                                }
                            >
                                <option value="">Task</option>

                                {selectedProject?.tasks?.map((task: any) => (
                                    <option key={task.id} value={task.id}>
                                        {task.task_number}
                                        {' — '}
                                        {task.title}
                                    </option>
                                ))}
                            </select>

                            <input
                                type="date"
                                className="rounded border p-2"
                                value={entryForm.data.work_date}
                                onChange={(e) =>
                                    entryForm.setData(
                                        'work_date',
                                        e.target.value,
                                    )
                                }
                            />

                            <input
                                type="number"
                                min="0.01"
                                max="24"
                                step="0.25"
                                className="rounded border p-2"
                                placeholder="Hours"
                                value={entryForm.data.hours}
                                onChange={(e) =>
                                    entryForm.setData(
                                        'hours',
                                        e.target.value
                                            ? Number(e.target.value)
                                            : '',
                                    )
                                }
                            />

                            <input
                                className="rounded border p-2 md:col-span-2"
                                placeholder="Work description"
                                value={entryForm.data.description}
                                onChange={(e) =>
                                    entryForm.setData(
                                        'description',
                                        e.target.value,
                                    )
                                }
                            />

                            <label className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={entryForm.data.billable}
                                    onChange={(e) =>
                                        entryForm.setData(
                                            'billable',
                                            e.target.checked,
                                        )
                                    }
                                />
                                Billable
                            </label>

                            <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                                Add Entry
                            </button>
                        </form>
                    </section>
                )}

                <section className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Date</th>

                                <th className="p-3 text-left">Project</th>

                                <th className="p-3 text-left">Task</th>

                                <th className="p-3 text-left">Description</th>

                                <th className="p-3 text-left">Hours</th>

                                <th className="p-3 text-left">Billable</th>

                                {editable && (
                                    <th className="p-3 text-left">Action</th>
                                )}
                            </tr>
                        </thead>

                        <tbody>
                            {timesheet.entries.map((entry: any) => (
                                <tr key={entry.public_id} className="border-t">
                                    <td className="p-3">{entry.work_date}</td>

                                    <td className="p-3">
                                        {entry.project.project_number}
                                    </td>

                                    <td className="p-3">
                                        {entry.task.task_number}
                                    </td>

                                    <td className="p-3">{entry.description}</td>

                                    <td className="p-3">{entry.hours}</td>

                                    <td className="p-3">
                                        {entry.billable ? 'Yes' : 'No'}
                                    </td>

                                    {editable && (
                                        <td className="p-3">
                                            <button
                                                onClick={() =>
                                                    router.delete(
                                                        `/admin/timesheets/${timesheet.public_id}/entries/${entry.public_id}`,
                                                        {
                                                            preserveScroll: true,
                                                        },
                                                    )
                                                }
                                                className="underline"
                                            >
                                                Remove
                                            </button>
                                        </td>
                                    )}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </section>

                {canReview && timesheet.status === 'submitted' && (
                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">
                            Review Timesheet
                        </h2>

                        <form
                            className="space-y-3"
                            onSubmit={(event) => {
                                event.preventDefault();

                                reviewForm.post(
                                    `/admin/timesheets/${timesheet.public_id}/review`,
                                );
                            }}
                        >
                            <select
                                className="w-full rounded border p-2"
                                value={reviewForm.data.decision}
                                onChange={(e) =>
                                    reviewForm.setData(
                                        'decision',
                                        e.target
                                            .value as ReviewForm['decision'],
                                    )
                                }
                            >
                                <option value="approved">Approve</option>

                                <option value="rejected">
                                    Return for Changes
                                </option>
                            </select>

                            <textarea
                                className="min-h-24 w-full rounded border p-2"
                                placeholder="Review notes"
                                value={reviewForm.data.review_notes}
                                onChange={(e) =>
                                    reviewForm.setData(
                                        'review_notes',
                                        e.target.value,
                                    )
                                }
                            />

                            <button className="rounded border px-4 py-2">
                                Save Review
                            </button>
                        </form>
                    </section>
                )}
            </div>
        </>
    );
}
