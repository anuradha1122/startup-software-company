import { Head, router, useForm } from '@inertiajs/react';

type ReviewForm = {
    decision: 'approved' | 'rejected';
    review_notes: string;
};

export default function ExpenseShow({ expense }: any) {
    const reviewForm = useForm<ReviewForm>({
        decision: 'approved',
        review_notes: '',
    });

    const editable = ['draft', 'rejected'].includes(expense.status);

    return (
        <>
            <Head title={expense.expense_number} />

            <div className="max-w-6xl space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">
                        {expense.expense_number}
                    </h1>

                    <p>{expense.title}</p>

                    <p className="text-sm text-muted-foreground">
                        {expense.project.project_number}
                        {' · '}
                        {expense.status}
                    </p>
                </div>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-3">
                    <div>
                        <span className="text-sm text-muted-foreground">
                            Type
                        </span>

                        <p>{expense.type}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Date
                        </span>

                        <p>{expense.expense_date}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Amount
                        </span>

                        <p>
                            {expense.currency} {expense.total_amount}
                        </p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Supplier
                        </span>

                        <p>{expense.supplier?.name ?? '-'}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Billable
                        </span>

                        <p>{expense.billable_to_client ? 'Yes' : 'No'}</p>
                    </div>
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Supporting Documents
                    </h2>

                    {expense.attachments.map((attachment: any) => (
                        <div
                            key={attachment.public_id}
                            className="mb-2 rounded border p-3"
                        >
                            <a
                                href={`/admin/expenses/${expense.public_id}/attachments/${attachment.public_id}/download`}
                                className="underline"
                            >
                                {attachment.title}
                            </a>
                        </div>
                    ))}
                </section>

                {editable && expense.attachments.length > 0 && (
                    <button
                        onClick={() =>
                            router.post(
                                `/admin/expenses/${expense.public_id}/submit`,
                            )
                        }
                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        Submit for Approval
                    </button>
                )}

                {expense.status === 'submitted' && (
                    <form
                        className="space-y-3 rounded border p-5"
                        onSubmit={(event) => {
                            event.preventDefault();

                            reviewForm.post(
                                `/admin/expenses/${expense.public_id}/review`,
                            );
                        }}
                    >
                        <h2 className="text-lg font-semibold">
                            Review Expense
                        </h2>

                        <select
                            className="w-full rounded border p-2"
                            value={reviewForm.data.decision}
                            onChange={(e) =>
                                reviewForm.setData(
                                    'decision',
                                    e.target.value as ReviewForm['decision'],
                                )
                            }
                        >
                            <option value="approved">Approve</option>

                            <option value="rejected">Return for Changes</option>
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
            </div>
        </>
    );
}
