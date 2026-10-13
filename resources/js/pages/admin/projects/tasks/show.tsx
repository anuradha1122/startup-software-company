import { Head, Link, router, useForm } from '@inertiajs/react';

type CommentForm = {
    body: string;
    client_visible: boolean;
};

type SubmissionForm = {
    summary: string;
    testing_notes: string;
    deployment_notes: string;
    reference_url: string;
    attachments: File[];
};

type ReviewForm = {
    decision: 'approved' | 'changes_required';
    review_notes: string;
};

export default function TaskShow({ task, statuses }: any) {
    const commentForm = useForm<CommentForm>({
        body: '',
        client_visible: false,
    });

    const submissionForm = useForm<SubmissionForm>({
        summary: '',
        testing_notes: '',
        deployment_notes: '',
        reference_url: '',
        attachments: [],
    });

    const reviewForm = useForm<ReviewForm>({
        decision: 'approved',
        review_notes: '',
    });

    const latestSubmission = task.submissions?.[0];

    return (
        <>
            <Head title={task.task_number} />

            <div className="max-w-6xl space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {task.task_number}
                        </h1>

                        <p className="text-lg">{task.title}</p>

                        <p className="text-sm text-muted-foreground">
                            {task.project.name}
                            {' · '}
                            {task.workflow_status.name}
                        </p>
                    </div>

                    <Link
                        href={`/admin/projects/${task.project.public_id}/tasks/board`}
                        className="rounded border px-3 py-2"
                    >
                        Task Board
                    </Link>
                </div>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-4">
                    <div>
                        <span className="text-sm text-muted-foreground">
                            Status
                        </span>

                        <select
                            className="mt-1 w-full rounded border p-2"
                            value={task.workflow_status_id}
                            onChange={(e) =>
                                router.post(
                                    `/admin/tasks/${task.public_id}/move`,
                                    {
                                        workflow_status_id: Number(
                                            e.target.value,
                                        ),
                                    },
                                    {
                                        preserveScroll: true,
                                    },
                                )
                            }
                        >
                            {statuses.map((status: any) => (
                                <option key={status.id} value={status.id}>
                                    {status.name}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Priority
                        </span>

                        <p>{task.priority}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Assignee
                        </span>

                        <p>{task.assignee?.name ?? 'Unassigned'}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Due
                        </span>

                        <p>{task.due_date ?? '-'}</p>
                    </div>
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-3 text-lg font-semibold">Description</h2>

                    <p className="whitespace-pre-wrap">{task.description}</p>
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">Dependencies</h2>

                    {task.dependencies.length === 0 ? (
                        <p className="text-sm text-muted-foreground">
                            No dependencies.
                        </p>
                    ) : (
                        task.dependencies.map((dependency: any) => (
                            <div
                                key={dependency.public_id}
                                className="mb-2 rounded border p-3"
                            >
                                <Link
                                    href={`/admin/tasks/${dependency.public_id}`}
                                    className="font-medium underline"
                                >
                                    {dependency.task_number}
                                </Link>

                                {' — '}
                                {dependency.title}

                                <span className="ml-2 text-sm text-muted-foreground">
                                    {dependency.workflow_status.name}
                                </span>
                            </div>
                        ))
                    )}
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Work Submission
                    </h2>

                    {task.project.status === 'active' && (
                        <form
                            className="space-y-3"
                            onSubmit={(event) => {
                                event.preventDefault();

                                submissionForm.post(
                                    `/admin/tasks/${task.public_id}/submissions`,
                                    {
                                        forceFormData: true,
                                    },
                                );
                            }}
                        >
                            <textarea
                                className="min-h-32 w-full rounded border p-2"
                                placeholder="Summary of completed work"
                                value={submissionForm.data.summary}
                                onChange={(e) =>
                                    submissionForm.setData(
                                        'summary',
                                        e.target.value,
                                    )
                                }
                            />

                            <textarea
                                className="min-h-24 w-full rounded border p-2"
                                placeholder="Testing notes"
                                value={submissionForm.data.testing_notes}
                                onChange={(e) =>
                                    submissionForm.setData(
                                        'testing_notes',
                                        e.target.value,
                                    )
                                }
                            />

                            <textarea
                                className="min-h-24 w-full rounded border p-2"
                                placeholder="Deployment notes"
                                value={submissionForm.data.deployment_notes}
                                onChange={(e) =>
                                    submissionForm.setData(
                                        'deployment_notes',
                                        e.target.value,
                                    )
                                }
                            />

                            <input
                                className="w-full rounded border p-2"
                                placeholder="Reference URL"
                                value={submissionForm.data.reference_url}
                                onChange={(e) =>
                                    submissionForm.setData(
                                        'reference_url',
                                        e.target.value,
                                    )
                                }
                            />

                            <input
                                type="file"
                                multiple
                                onChange={(e) =>
                                    submissionForm.setData(
                                        'attachments',
                                        Array.from(e.target.files ?? []),
                                    )
                                }
                            />

                            <button
                                disabled={submissionForm.processing}
                                className="rounded bg-primary px-4 py-2 text-primary-foreground"
                            >
                                Submit Work
                            </button>
                        </form>
                    )}

                    <div className="mt-6 space-y-4">
                        {task.submissions.map((submission: any) => (
                            <div
                                key={submission.public_id}
                                className="rounded border p-4"
                            >
                                <div className="flex justify-between">
                                    <strong>
                                        Version {submission.version_number}
                                    </strong>

                                    <span>{submission.status}</span>
                                </div>

                                <p className="mt-2 whitespace-pre-wrap">
                                    {submission.summary}
                                </p>

                                {submission.attachments.map(
                                    (attachment: any) => (
                                        <a
                                            key={attachment.public_id}
                                            href={`/admin/work-submissions/${submission.public_id}/attachments/${attachment.public_id}/download`}
                                            className="mt-3 mr-3 inline-block underline"
                                        >
                                            {attachment.original_name}
                                        </a>
                                    ),
                                )}
                            </div>
                        ))}
                    </div>

                    {latestSubmission?.status === 'submitted' && (
                        <form
                            className="mt-6 space-y-3 rounded border p-4"
                            onSubmit={(event) => {
                                event.preventDefault();

                                reviewForm.post(
                                    `/admin/work-submissions/${latestSubmission.public_id}/review`,
                                );
                            }}
                        >
                            <h3 className="font-semibold">
                                Review Latest Submission
                            </h3>

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
                                <option value="approved">Approved</option>

                                <option value="changes_required">
                                    Changes Required
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
                    )}
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">Comments</h2>

                    <form
                        className="mb-5 space-y-3"
                        onSubmit={(event) => {
                            event.preventDefault();

                            commentForm.post(
                                `/admin/tasks/${task.public_id}/comments`,
                                {
                                    preserveScroll: true,

                                    onSuccess: () => commentForm.reset(),
                                },
                            );
                        }}
                    >
                        <textarea
                            className="min-h-24 w-full rounded border p-2"
                            value={commentForm.data.body}
                            onChange={(e) =>
                                commentForm.setData('body', e.target.value)
                            }
                        />

                        <button className="rounded border px-4 py-2">
                            Add Comment
                        </button>
                    </form>

                    {task.comments.map((comment: any) => (
                        <div
                            key={comment.public_id}
                            className="mb-3 rounded border p-3"
                        >
                            <strong>{comment.creator?.name}</strong>

                            <p className="mt-1 whitespace-pre-wrap">
                                {comment.body}
                            </p>
                        </div>
                    ))}
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Workflow History
                    </h2>

                    {task.transitions.map((transition: any) => (
                        <div
                            key={transition.id}
                            className="mb-2 border-b pb-2 text-sm"
                        >
                            {transition.from_status?.name ?? 'Created'}
                            {' → '}
                            {transition.to_status?.name}

                            {' · '}

                            {transition.changed_by?.name}
                        </div>
                    ))}
                </section>
            </div>
        </>
    );
}
