import { Head, Link, router, useForm } from '@inertiajs/react';

type InvoiceLine = {
    id: number;
    public_id: string;
    sort_order: number;
    description: string;
    quantity: string;
    unit_price: string;
    tax_rate: string;
    subtotal: string;
    tax_amount: string;
    total_amount: string;
    source_type?: string | null;
    source_id?: number | null;
};

type BillableExpense = {
    id: number;
    public_id: string;
    expense_number: string;
    title: string;
    expense_date: string;
    currency: string;
    total_amount: string;
};

type PaymentAllocation = {
    id: number;
    public_id: string;
    amount: string;
    payment: {
        public_id: string;
        payment_number: string;
        payment_date: string;
        currency: string;
    };
};

type Invoice = {
    id: number;
    public_id: string;
    invoice_number: string;
    status: 'draft' | 'issued' | 'partially_paid' | 'paid' | 'overdue' | 'void';
    invoice_date: string;
    due_date: string;
    currency: string;
    billing_name: string;
    billing_email?: string | null;
    billing_address?: string | null;
    notes?: string | null;
    payment_instructions?: string | null;
    subtotal: string;
    tax_amount: string;
    total_amount: string;
    amount_paid: string;
    balance_due: string;
    issued_at?: string | null;
    last_sent_at?: string | null;
    paid_at?: string | null;
    client: {
        public_id: string;
        name: string;
    };
    project?: {
        public_id: string;
        project_number: string;
        name: string;
    } | null;
    lines: InvoiceLine[];
    payment_allocations: PaymentAllocation[];
};

type Props = {
    invoice: Invoice;
    billableExpenses: BillableExpense[];
};

type InvoiceLineForm = {
    description: string;
    quantity: number | '';
    unit_price: number | '';
    tax_rate: number | '';
};

export default function InvoiceShow({ invoice, billableExpenses }: Props) {
    const lineForm = useForm<InvoiceLineForm>({
        description: '',
        quantity: 1,
        unit_price: '',
        tax_rate: 0,
    });

    const editable = invoice.status === 'draft';

    return (
        <>
            <Head title={invoice.invoice_number} />

            <div className="max-w-7xl space-y-6 p-6">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {invoice.invoice_number}
                        </h1>

                        <p>{invoice.client.name}</p>

                        <p className="text-sm text-muted-foreground">
                            Status: {invoice.status}
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        <a
                            href={`/admin/invoices/${invoice.public_id}/pdf`}
                            className="rounded border px-3 py-2"
                        >
                            PDF
                        </a>

                        {editable && (
                            <button
                                type="button"
                                onClick={() =>
                                    router.post(
                                        `/admin/invoices/${invoice.public_id}/issue`,
                                    )
                                }
                                className="rounded bg-primary px-3 py-2 text-primary-foreground"
                            >
                                Issue Invoice
                            </button>
                        )}

                        {!editable && invoice.status !== 'void' && (
                            <button
                                type="button"
                                onClick={() =>
                                    router.post(
                                        `/admin/invoices/${invoice.public_id}/send`,
                                    )
                                }
                                className="rounded border px-3 py-2"
                            >
                                Send Again
                            </button>
                        )}

                        <Link
                            href="/admin/invoices"
                            className="rounded border px-3 py-2"
                        >
                            Back
                        </Link>
                    </div>
                </div>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-4">
                    <div>
                        <p className="text-sm text-muted-foreground">
                            Invoice Date
                        </p>

                        <p>{invoice.invoice_date}</p>
                    </div>

                    <div>
                        <p className="text-sm text-muted-foreground">
                            Due Date
                        </p>

                        <p>{invoice.due_date}</p>
                    </div>

                    <div>
                        <p className="text-sm text-muted-foreground">Project</p>

                        <p>
                            {invoice.project
                                ? `${invoice.project.project_number} — ${invoice.project.name}`
                                : '-'}
                        </p>
                    </div>

                    <div>
                        <p className="text-sm text-muted-foreground">
                            Billing Email
                        </p>

                        <p>{invoice.billing_email ?? '-'}</p>
                    </div>
                </section>

                {editable && (
                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">
                            Add Invoice Line
                        </h2>

                        <form
                            className="grid gap-3 md:grid-cols-4"
                            onSubmit={(event) => {
                                event.preventDefault();

                                lineForm.post(
                                    `/admin/invoices/${invoice.public_id}/lines`,
                                    {
                                        preserveScroll: true,

                                        onSuccess: () => lineForm.reset(),
                                    },
                                );
                            }}
                        >
                            <input
                                className="rounded border p-2 md:col-span-2"
                                placeholder="Description"
                                value={lineForm.data.description}
                                onChange={(event) =>
                                    lineForm.setData(
                                        'description',
                                        event.target.value,
                                    )
                                }
                            />

                            <input
                                type="number"
                                min="0.01"
                                step="0.01"
                                className="rounded border p-2"
                                placeholder="Quantity"
                                value={lineForm.data.quantity}
                                onChange={(event) =>
                                    lineForm.setData(
                                        'quantity',
                                        event.target.value
                                            ? Number(event.target.value)
                                            : '',
                                    )
                                }
                            />

                            <input
                                type="number"
                                min="0"
                                step="0.01"
                                className="rounded border p-2"
                                placeholder="Unit price"
                                value={lineForm.data.unit_price}
                                onChange={(event) =>
                                    lineForm.setData(
                                        'unit_price',
                                        event.target.value
                                            ? Number(event.target.value)
                                            : '',
                                    )
                                }
                            />

                            <input
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                className="rounded border p-2"
                                placeholder="Tax %"
                                value={lineForm.data.tax_rate}
                                onChange={(event) =>
                                    lineForm.setData(
                                        'tax_rate',
                                        event.target.value
                                            ? Number(event.target.value)
                                            : 0,
                                    )
                                }
                            />

                            <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                                Add Line
                            </button>
                        </form>
                    </section>
                )}

                <section className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Description</th>

                                <th className="p-3 text-right">Qty</th>

                                <th className="p-3 text-right">Unit Price</th>

                                <th className="p-3 text-right">Tax %</th>

                                <th className="p-3 text-right">Total</th>

                                {editable && (
                                    <th className="p-3 text-left">Action</th>
                                )}
                            </tr>
                        </thead>

                        <tbody>
                            {invoice.lines.map((line) => (
                                <tr key={line.public_id} className="border-t">
                                    <td className="p-3">
                                        {line.description}

                                        {line.source_type ===
                                            'project_expense' && (
                                            <p className="text-xs text-muted-foreground">
                                                Imported from approved project
                                                expense
                                            </p>
                                        )}
                                    </td>

                                    <td className="p-3 text-right">
                                        {line.quantity}
                                    </td>

                                    <td className="p-3 text-right">
                                        {invoice.currency}{' '}
                                        {Number(line.unit_price).toFixed(2)}
                                    </td>

                                    <td className="p-3 text-right">
                                        {line.tax_rate}
                                    </td>

                                    <td className="p-3 text-right">
                                        {invoice.currency}{' '}
                                        {Number(line.total_amount).toFixed(2)}
                                    </td>

                                    {editable && (
                                        <td className="p-3">
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    router.delete(
                                                        `/admin/invoices/${invoice.public_id}/lines/${line.public_id}`,
                                                        {
                                                            preserveScroll: true,
                                                        },
                                                    )
                                                }
                                                className="text-destructive underline"
                                            >
                                                Remove
                                            </button>
                                        </td>
                                    )}
                                </tr>
                            ))}

                            {invoice.lines.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={editable ? 6 : 5}
                                        className="p-6 text-center text-muted-foreground"
                                    >
                                        No invoice lines yet.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </section>

                {editable && billableExpenses.length > 0 && (
                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">
                            Approved Billable Expenses
                        </h2>

                        <div className="space-y-3">
                            {billableExpenses.map((expense) => (
                                <div
                                    key={expense.public_id}
                                    className="flex flex-wrap items-center justify-between gap-3 rounded border p-3"
                                >
                                    <div>
                                        <p className="font-medium">
                                            {expense.expense_number}
                                            {' — '}
                                            {expense.title}
                                        </p>

                                        <p className="text-sm text-muted-foreground">
                                            {expense.expense_date}
                                            {' · '}
                                            {expense.currency}{' '}
                                            {Number(
                                                expense.total_amount,
                                            ).toFixed(2)}
                                        </p>
                                    </div>

                                    <button
                                        type="button"
                                        onClick={() =>
                                            router.post(
                                                `/admin/invoices/${invoice.public_id}/expenses/${expense.public_id}/import`,
                                                {},
                                                {
                                                    preserveScroll: true,
                                                },
                                            )
                                        }
                                        className="rounded border px-3 py-2"
                                    >
                                        Import
                                    </button>
                                </div>
                            ))}
                        </div>
                    </section>
                )}

                <section className="ml-auto max-w-lg rounded border p-5">
                    <div className="space-y-2">
                        <div className="flex justify-between">
                            <span>Subtotal</span>

                            <strong>
                                {invoice.currency}{' '}
                                {Number(invoice.subtotal).toFixed(2)}
                            </strong>
                        </div>

                        <div className="flex justify-between">
                            <span>Tax</span>

                            <strong>
                                {invoice.currency}{' '}
                                {Number(invoice.tax_amount).toFixed(2)}
                            </strong>
                        </div>

                        <div className="flex justify-between border-t pt-2 text-lg">
                            <span>Total</span>

                            <strong>
                                {invoice.currency}{' '}
                                {Number(invoice.total_amount).toFixed(2)}
                            </strong>
                        </div>

                        <div className="flex justify-between">
                            <span>Paid</span>

                            <strong>
                                {invoice.currency}{' '}
                                {Number(invoice.amount_paid).toFixed(2)}
                            </strong>
                        </div>

                        <div className="flex justify-between">
                            <span>Balance</span>

                            <strong>
                                {invoice.currency}{' '}
                                {Number(invoice.balance_due).toFixed(2)}
                            </strong>
                        </div>
                    </div>
                </section>

                {invoice.payment_allocations.length > 0 && (
                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">
                            Payment Allocations
                        </h2>

                        <div className="space-y-2">
                            {invoice.payment_allocations.map((allocation) => (
                                <div
                                    key={allocation.public_id}
                                    className="flex flex-wrap justify-between gap-3 rounded border p-3"
                                >
                                    <Link
                                        href={`/admin/payments/${allocation.payment.public_id}`}
                                        className="underline"
                                    >
                                        {allocation.payment.payment_number}
                                    </Link>

                                    <span>
                                        {allocation.payment.currency}{' '}
                                        {Number(allocation.amount).toFixed(2)}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </section>
                )}
            </div>
        </>
    );
}
