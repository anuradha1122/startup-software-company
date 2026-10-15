import { Head, Link, useForm } from '@inertiajs/react';

type Props = {
    approval: {
        public_id: string;
        approval_number: string;
        type: string;
        title: string;
        summary?: string | null;
        status: string;
        requested_at: string;
        due_at?: string | null;
        response_comment?: string | null;
        responded_at?: string | null;
        project: {
            public_id: string;
            project_number: string;
            name: string;
        };
    };
};

type ResponseForm = {
    decision: 'approved' | 'rejected';
    response_comment: string;
};

export default function ApprovalShow({ approval }: Props) {
    const form = useForm<ResponseForm>({
        decision: 'approved',
        response_comment: '',
    });

    return (
        <>
            <Head title={approval.approval_number} />

            <main className="mx-auto max-w-4xl space-y-6 p-6">
                <Link href="/portal/approvals" className="text-sm underline">
                    Back to Approvals
                </Link>

                <section className="rounded border p-6">
                    <p className="text-sm text-muted-foreground">
                        {approval.approval_number}
                    </p>

                    <h1 className="mt-1 text-2xl font-semibold">
                        {approval.title}
                    </h1>

                    <p className="mt-2">
                        {approval.project.project_number}
                        {' — '}
                        {approval.project.name}
                    </p>

                    <p className="mt-4 whitespace-pre-wrap">
                        {approval.summary ??
                            'No additional summary was provided.'}
                    </p>

                    <div className="mt-4 text-sm text-muted-foreground">
                        Status: {approval.status}
                    </div>
                </section>

                {approval.status === 'pending' && (
                    <form
                        className="space-y-4 rounded border p-6"
                        onSubmit={(event) => {
                            event.preventDefault();

                            form.post(
                                `/portal/approvals/${approval.public_id}/respond`,
                            );
                        }}
                    >
                        <h2 className="text-lg font-semibold">Your Decision</h2>

                        <select
                            value={form.data.decision}
                            onChange={(event) =>
                                form.setData(
                                    'decision',
                                    event.target
                                        .value as ResponseForm['decision'],
                                )
                            }
                            className="w-full rounded border p-2"
                        >
                            <option value="approved">Approve</option>

                            <option value="rejected">Reject</option>
                        </select>

                        <textarea
                            value={form.data.response_comment}
                            onChange={(event) =>
                                form.setData(
                                    'response_comment',
                                    event.target.value,
                                )
                            }
                            placeholder={
                                form.data.decision === 'rejected'
                                    ? 'Reason for rejection is required'
                                    : 'Optional comment'
                            }
                            className="min-h-32 w-full rounded border p-2"
                        />

                        <button
                            disabled={form.processing}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            Submit Decision
                        </button>
                    </form>
                )}
            </main>
        </>
    );
}
