import { Head, Link } from '@inertiajs/react';

export default function ProposalIndex({ proposals }: any) {
    return (
        <>
            <Head title="Proposals" />

            <div className="p-6">
                <div className="mb-6 flex justify-between">
                    <h1 className="text-2xl font-semibold">Proposals</h1>

                    <Link
                        href="/admin/proposals/create"
                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        New Proposal
                    </Link>
                </div>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Proposal</th>
                                <th className="p-3 text-left">Client</th>
                                <th className="p-3 text-left">Status</th>
                                <th className="p-3 text-left">Valid Until</th>
                            </tr>
                        </thead>

                        <tbody>
                            {proposals.data.map((proposal: any) => (
                                <tr
                                    key={proposal.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/proposals/${proposal.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {proposal.proposal_number}
                                        </Link>

                                        <p>{proposal.title}</p>
                                    </td>

                                    <td className="p-3">
                                        {proposal.client?.name}
                                    </td>

                                    <td className="p-3">{proposal.status}</td>

                                    <td className="p-3">
                                        {proposal.valid_until}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
