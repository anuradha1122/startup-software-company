import { Head, Link, router } from '@inertiajs/react';

type InvoiceListItem = {
    id: number;
    public_id: string;
    invoice_number: string;
    status: 'draft' | 'issued' | 'partially_paid' | 'paid' | 'overdue' | 'void';
    invoice_date: string;
    due_date: string;
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

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedInvoices = {
    data: InvoiceListItem[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
};

type InvoiceFilters = {
    search?: string | null;
    status?: string | null;
};

type Props = {
    invoices: PaginatedInvoices;
    filters: InvoiceFilters;
};

export default function InvoiceIndex({ invoices, filters }: Props) {
    const submitFilters = (event: React.FormEvent<HTMLFormElement>) => {
        event.preventDefault();

        const formData = new FormData(event.currentTarget);

        router.get('/admin/invoices', Object.fromEntries(formData.entries()), {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <>
            <Head title="Invoices" />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">Invoices</h1>

                        <p className="text-sm text-muted-foreground">
                            Client invoices, balances and billing status.
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        <a
                            href="/admin/invoices/export/excel"
                            className="rounded border px-4 py-2"
                        >
                            Export Excel
                        </a>

                        <Link
                            href="/admin/invoices/create"
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            New Invoice
                        </Link>
                    </div>
                </div>

                <form
                    onSubmit={submitFilters}
                    className="grid gap-3 rounded border p-4 md:grid-cols-3"
                >
                    <input
                        name="search"
                        placeholder="Invoice or client"
                        defaultValue={filters.search ?? ''}
                        className="rounded border p-2"
                    />

                    <select
                        name="status"
                        defaultValue={filters.status ?? ''}
                        className="rounded border p-2"
                    >
                        <option value="">All statuses</option>

                        <option value="draft">Draft</option>

                        <option value="issued">Issued</option>

                        <option value="partially_paid">Partially Paid</option>

                        <option value="paid">Paid</option>

                        <option value="overdue">Overdue</option>

                        <option value="void">Void</option>
                    </select>

                    <button className="rounded border px-4 py-2">Filter</button>
                </form>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Invoice</th>

                                <th className="p-3 text-left">Client</th>

                                <th className="p-3 text-left">Project</th>

                                <th className="p-3 text-left">Invoice Date</th>

                                <th className="p-3 text-left">Due</th>

                                <th className="p-3 text-left">Total</th>

                                <th className="p-3 text-left">Paid</th>

                                <th className="p-3 text-left">Balance</th>

                                <th className="p-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            {invoices.data.map((invoice) => (
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

                                    <td className="p-3">
                                        {invoice.invoice_date}
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

                                    <td className="p-3">
                                        {invoice.currency}{' '}
                                        {Number(invoice.balance_due).toFixed(2)}
                                    </td>

                                    <td className="p-3">{invoice.status}</td>
                                </tr>
                            ))}

                            {invoices.data.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={9}
                                        className="p-6 text-center text-muted-foreground"
                                    >
                                        No invoices found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {invoices.last_page > 1 && (
                    <div className="flex flex-wrap gap-2">
                        {invoices.links.map((link, index) => (
                            <button
                                key={`${link.label}-${index}`}
                                type="button"
                                disabled={!link.url}
                                onClick={() => {
                                    if (link.url) {
                                        router.visit(link.url, {
                                            preserveScroll: true,
                                        });
                                    }
                                }}
                                className={`rounded border px-3 py-1 ${
                                    link.active
                                        ? 'bg-primary text-primary-foreground'
                                        : ''
                                }`}
                                dangerouslySetInnerHTML={{
                                    __html: link.label,
                                }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}
