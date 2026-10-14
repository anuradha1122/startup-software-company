import { Head, Link } from '@inertiajs/react';

type ReceivableInvoice = {
    id: number;
    public_id: string;
    invoice_number: string;
    due_date: string;
    status: 'issued' | 'partially_paid' | 'overdue';
    currency: string;
    total_amount: string;
    amount_paid: string;
    balance_due: string;
    client: {
        name: string;
    };
    project?: {
        project_number: string;
        name: string;
    } | null;
};

type Props = {
    invoices: ReceivableInvoice[];
    summary: {
        outstanding: number;
        overdue: number;
        count: number;
    };
};

export default function ReceivablesIndex({ invoices, summary }: Props) {
    return (
        <>
            <Head title="Receivables" />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Accounts Receivable
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            Outstanding and overdue client invoice balances.
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href="/admin/receivables/export/excel"
                            className="rounded border px-4 py-2"
                        >
                            Excel
                        </a>

                        <a
                            href="/admin/receivables/export/pdf"
                            className="rounded border px-4 py-2"
                        >
                            PDF
                        </a>
                    </div>
                </div>

                <section className="grid gap-4 md:grid-cols-3">
                    <div className="rounded border p-4">
                        <p className="text-sm text-muted-foreground">
                            Outstanding Invoices
                        </p>

                        <p className="text-2xl font-semibold">
                            {summary.count}
                        </p>
                    </div>

                    <div className="rounded border p-4">
                        <p className="text-sm text-muted-foreground">
                            Outstanding Balance
                        </p>

                        <p className="text-2xl font-semibold">
                            {Number(summary.outstanding).toFixed(2)}
                        </p>
                    </div>

                    <div className="rounded border p-4">
                        <p className="text-sm text-muted-foreground">
                            Overdue Balance
                        </p>

                        <p className="text-2xl font-semibold">
                            {Number(summary.overdue).toFixed(2)}
                        </p>
                    </div>
                </section>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Invoice</th>

                                <th className="p-3 text-left">Client</th>

                                <th className="p-3 text-left">Project</th>

                                <th className="p-3 text-left">Due Date</th>

                                <th className="p-3 text-left">Total</th>

                                <th className="p-3 text-left">Paid</th>

                                <th className="p-3 text-left">Outstanding</th>

                                <th className="p-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            {invoices.map((invoice) => (
                                <tr
                                    key={invoice.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/invoices/${invoice.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {invoice.invoice_number}
                                        </Link>
                                    </td>

                                    <td className="p-3">
                                        {invoice.client.name}
                                    </td>

                                    <td className="p-3">
                                        {invoice.project
                                            ? `${invoice.project.project_number} — ${invoice.project.name}`
                                            : '-'}
                                    </td>

                                    <td className="p-3">{invoice.due_date}</td>

                                    <td className="p-3">
                                        {invoice.currency}{' '}
                                        {Number(invoice.total_amount).toFixed(
                                            2,
                                        )}
                                    </td>

                                    <td className="p-3">
                                        {invoice.currency}{' '}
                                        {Number(invoice.amount_paid).toFixed(2)}
                                    </td>

                                    <td className="p-3 font-medium">
                                        {invoice.currency}{' '}
                                        {Number(invoice.balance_due).toFixed(2)}
                                    </td>

                                    <td className="p-3">{invoice.status}</td>
                                </tr>
                            ))}

                            {invoices.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={8}
                                        className="p-6 text-center text-muted-foreground"
                                    >
                                        No outstanding receivables.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
