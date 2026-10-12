import { Head, Link } from '@inertiajs/react';

export default function ContractIndex({ contracts }: any) {
    return (
        <>
            <Head title="Contracts" />

            <div className="p-6">
                <h1 className="mb-6 text-2xl font-semibold">Contracts</h1>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Contract</th>
                                <th className="p-3 text-left">Client</th>
                                <th className="p-3 text-left">Value</th>
                                <th className="p-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            {contracts.data.map((contract: any) => (
                                <tr
                                    key={contract.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/contracts/${contract.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {contract.contract_number}
                                        </Link>
                                    </td>

                                    <td className="p-3">
                                        {contract.client?.name}
                                    </td>

                                    <td className="p-3">
                                        {contract.currency}{' '}
                                        {contract.contract_value}
                                    </td>

                                    <td className="p-3">{contract.status}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
