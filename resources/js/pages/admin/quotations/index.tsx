import { Head, Link } from '@inertiajs/react';

export default function QuotationIndex({ quotations }: any) {
    return (
        <>
            <Head title="Quotations" />

            <div className="p-6">
                <div className="mb-6 flex justify-between">
                    <h1 className="text-2xl font-semibold">Quotations</h1>

                    <Link
                        href="/admin/quotations/create"
                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        New Quotation
                    </Link>
                </div>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Quotation</th>
                                <th className="p-3 text-left">Client</th>
                                <th className="p-3 text-left">Total</th>
                                <th className="p-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            {quotations.data.map((quotation: any) => (
                                <tr
                                    key={quotation.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/quotations/${quotation.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {quotation.quotation_number}
                                        </Link>
                                    </td>

                                    <td className="p-3">
                                        {quotation.client?.name}
                                    </td>

                                    <td className="p-3">
                                        {quotation.currency}{' '}
                                        {quotation.total_amount}
                                    </td>

                                    <td className="p-3">{quotation.status}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
