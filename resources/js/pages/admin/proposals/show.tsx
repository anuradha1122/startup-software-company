import { Head, Link, router, useForm } from '@inertiajs/react';

export default function ProposalShow({ proposal }: any) {
    const decision = useForm({
        decision: 'accepted',
        decision_notes: '',
    });

    return (
        <>
            <Head title={proposal.proposal_number} />

            <div className="max-w-6xl p-6">
                <div className="mb-6 flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {proposal.proposal_number}
                        </h1>

                        <p>{proposal.title}</p>

                        <p className="text-sm text-muted-foreground">
                            {proposal.client?.name}
                            {' · '}
                            {proposal.status}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/proposals/${proposal.public_id}/pdf`}
                            className="rounded border px-3 py-2"
                        >
                            PDF
                        </a>

                        {proposal.status === 'draft' && (
                            <button
                                onClick={() =>
                                    router.post(
                                        `/admin/proposals/${proposal.public_id}/approve`,
                                    )
                                }
                                className="rounded border px-3 py-2"
                            >
                                Approve
                            </button>
                        )}

                        {proposal.status === 'approved' && (
                            <button
                                onClick={() =>
                                    router.post(
                                        `/admin/proposals/${proposal.public_id}/send`,
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
                    <h2 className="font-semibold">Executive Summary</h2>

                    <p className="whitespace-pre-wrap">
                        {proposal.executive_summary}
                    </p>

                    <h2 className="mt-6 font-semibold">Solution Overview</h2>

                    <p className="whitespace-pre-wrap">
                        {proposal.solution_overview}
                    </p>

                    <h2 className="mt-6 font-semibold">Delivery Approach</h2>

                    <p className="whitespace-pre-wrap">
                        {proposal.delivery_approach}
                    </p>
                </section>

                {proposal.status === 'sent' && (
                    <form
                        className="mt-6 max-w-xl space-y-3 rounded border p-5"
                        onSubmit={(event) => {
                            event.preventDefault();

                            decision.post(
                                `/admin/proposals/${proposal.public_id}/decision`,
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
                            placeholder="Decision notes"
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

                {proposal.status === 'accepted' && (
                    <div className="mt-6">
                        <Link
                            href={`/admin/quotations/create?proposal=${proposal.public_id}`}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            Create Quotation
                        </Link>
                    </div>
                )}
            </div>
        </>
    );
}
