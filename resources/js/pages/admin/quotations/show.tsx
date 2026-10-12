import { Head, Link, router, useForm } from '@inertiajs/react';

export default function QuotationShow({ quotation }: any) {
    const decision = useForm({
        decision: 'accepted',
        decision_notes: '',
    });

    return (
        <>
            <Head title={quotation.quotation_number} />

            <div className="max-w-6xl p-6">
                <div className="mb-6 flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {quotation.quotation_number}
                        </h1>

                        <p>{quotation.client?.name}</p>

                        <p>
                            {quotation.currency} {quotation.total_amount}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/quotations/${quotation.public_id}/pdf`}
                            className="rounded border px-3 py-2"
                        >
                            PDF
                        </a>

                        <a
                            href={`/admin/quotations/${quotation.public_id}/excel`}
                            className="rounded border px-3 py-2"
                        >
                            Excel
                        </a>

                        {quotation.status === 'draft' && (
                            <button
                                onClick={() =>
                                    router.post(
                                        `/admin/quotations/${quotation.public_id}/approve`,
                                    )
                                }
                                className="rounded border px-3 py-2"
                            >
                                Approve
                            </button>
                        )}

                        {quotation.status === 'approved' && (
                            <button
                                onClick={() =>
                                    router.post(
                                        `/admin/quotations/${quotation.public_id}/send`,
                                    )
                                }
                                className="rounded bg-primary px-3 py-2 text-primary-foreground"
                            >
                                Send
                            </button>
                        )}
                    </div>
                </div>

                <section className="rounded border p-5">
                    <dl className="grid gap-4 md:grid-cols-2">
                        <div>
                            <dt>Subtotal</dt>
                            <dd>{quotation.subtotal}</dd>
                        </div>

                        <div>
                            <dt>Discount</dt>
                            <dd>{quotation.discount_amount}</dd>
                        </div>

                        <div>
                            <dt>Tax</dt>
                            <dd>{quotation.tax_amount}</dd>
                        </div>

                        <div>
                            <dt>Total</dt>
                            <dd className="font-semibold">
                                {quotation.total_amount}
                            </dd>
                        </div>
                    </dl>
                </section>

                {quotation.status === 'sent' && (
                    <form
                        className="mt-6 max-w-xl space-y-3 rounded border p-5"
                        onSubmit={(event) => {
                            event.preventDefault();

                            decision.post(
                                `/admin/quotations/${quotation.public_id}/decision`,
                            );
                        }}
                    >
                        <select
                            className="w-full rounded border p-2"
                            value={decision.data.decision}
                            onChange={(e) =>
                                decision.setData('decision', e.target.value)
                            }
                        >
                            <option value="accepted">Accepted</option>

                            <option value="rejected">Rejected</option>
                        </select>

                        <textarea
                            className="min-h-24 w-full rounded border p-2"
                            value={decision.data.decision_notes}
                            onChange={(e) =>
                                decision.setData(
                                    'decision_notes',
                                    e.target.value,
                                )
                            }
                        />

                        <button className="rounded border px-4 py-2">
                            Record Decision
                        </button>
                    </form>
                )}

                {quotation.status === 'accepted' && !quotation.contract && (
                    <div className="mt-6">
                        <Link
                            href={`/admin/quotations/${quotation.public_id}/contract/create`}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            Create Contract
                        </Link>
                    </div>
                )}
            </div>
        </>
    );
}
