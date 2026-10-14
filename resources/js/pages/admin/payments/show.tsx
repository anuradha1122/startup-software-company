import { Head, Link, router, useForm } from '@inertiajs/react';

type OutstandingInvoice = {
    id: number;
    public_id: string;
    invoice_number: string;
    due_date: string;
    status: string;
    currency: string;
    total_amount: string;
    balance_due: string;
};

type Allocation = {
    id: number;
    public_id: string;
    amount: string;
    invoice: {
        public_id: string;
        invoice_number: string;
        currency: string;
        status: string;
    };
};

type Payment = {
    id: number;
    public_id: string;
    payment_number: string;
    payment_date: string;
    currency: string;
    amount: string;
    allocated_amount: string;
    unallocated_amount: string;
    method: string;
    reference_number?: string | null;
    notes?: string | null;
    receipt_sent_at?: string | null;
    client: {
        public_id: string;
        name: string;
    };
    allocations: Allocation[];
};

type Props = {
    payment: Payment;
    invoices: OutstandingInvoice[];
};

type AllocationForm = {
    invoice_id: string;
    amount: number | '';
};

export default function PaymentShow({ payment, invoices }: Props) {
    const form = useForm<AllocationForm>({
        invoice_id: '',
        amount: '',
    });

    const selectedInvoice = invoices.find(
        (invoice) => String(invoice.id) === form.data.invoice_id,
    );

    return (
        <>
            <Head title={payment.payment_number} />

            <div className="max-w-6xl space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {payment.payment_number}
                        </h1>

                        <p>{payment.client.name}</p>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        <a
                            href={`/admin/payments/${payment.public_id}/pdf`}
                            className="rounded border px-3 py-2"
                        >
                            Receipt PDF
                        </a>

                        <button
                            type="button"
                            onClick={() =>
                                router.post(
                                    `/admin/payments/${payment.public_id}/send-receipt`,
                                )
                            }
                            className="rounded border px-3 py-2"
                        >
                            Send Receipt
                        </button>

                        <Link
                            href="/admin/payments"
                            className="rounded border px-3 py-2"
                        >
                            Back
                        </Link>
                    </div>
                </div>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-4">
                    <div>
                        <p className="text-sm text-muted-foreground">Date</p>

                        <p>{payment.payment_date}</p>
                    </div>

                    <div>
                        <p className="text-sm text-muted-foreground">Method</p>

                        <p>{payment.method}</p>
                    </div>

                    <div>
                        <p className="text-sm text-muted-foreground">
                            Reference
                        </p>

                        <p>{payment.reference_number ?? '-'}</p>
                    </div>

                    <div>
                        <p className="text-sm text-muted-foreground">
                            Receipt Sent
                        </p>

                        <p>{payment.receipt_sent_at ?? 'Not sent'}</p>
                    </div>
                </section>

                <section className="grid gap-4 md:grid-cols-3">
                    <div className="rounded border p-4">
                        <p className="text-sm text-muted-foreground">
                            Payment Amount
                        </p>

                        <p className="text-2xl font-semibold">
                            {payment.currency}{' '}
                            {Number(payment.amount).toFixed(2)}
                        </p>
                    </div>

                    <div className="rounded border p-4">
                        <p className="text-sm text-muted-foreground">
                            Allocated
                        </p>

                        <p className="text-2xl font-semibold">
                            {payment.currency}{' '}
                            {Number(payment.allocated_amount).toFixed(2)}
                        </p>
                    </div>

                    <div className="rounded border p-4">
                        <p className="text-sm text-muted-foreground">
                            Unallocated
                        </p>

                        <p className="text-2xl font-semibold">
                            {payment.currency}{' '}
                            {Number(payment.unallocated_amount).toFixed(2)}
                        </p>
                    </div>
                </section>

                {Number(payment.unallocated_amount) > 0 && (
                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">
                            Allocate Payment
                        </h2>

                        <form
                            className="grid gap-3 md:grid-cols-3"
                            onSubmit={(event) => {
                                event.preventDefault();

                                form.post(
                                    `/admin/payments/${payment.public_id}/allocations`,
                                    {
                                        preserveScroll: true,

                                        onSuccess: () => form.reset(),
                                    },
                                );
                            }}
                        >
                            <select
                                value={form.data.invoice_id}
                                onChange={(event) =>
                                    form.setData(
                                        'invoice_id',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                            >
                                <option value="">Select invoice</option>

                                {invoices.map((invoice) => (
                                    <option key={invoice.id} value={invoice.id}>
                                        {invoice.invoice_number}
                                        {' — '}
                                        {invoice.currency} {invoice.balance_due}
                                    </option>
                                ))}
                            </select>

                            <input
                                type="number"
                                min="0.01"
                                step="0.01"
                                max={
                                    selectedInvoice
                                        ? Math.min(
                                              Number(
                                                  payment.unallocated_amount,
                                              ),
                                              Number(
                                                  selectedInvoice.balance_due,
                                              ),
                                          )
                                        : Number(payment.unallocated_amount)
                                }
                                placeholder="Allocation amount"
                                value={form.data.amount}
                                onChange={(event) =>
                                    form.setData(
                                        'amount',
                                        event.target.value
                                            ? Number(event.target.value)
                                            : '',
                                    )
                                }
                                className="rounded border p-2"
                            />

                            <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                                Allocate
                            </button>
                        </form>
                    </section>
                )}

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Allocation History
                    </h2>

                    {payment.allocations.length > 0 ? (
                        <div className="space-y-3">
                            {payment.allocations.map((allocation) => (
                                <div
                                    key={allocation.public_id}
                                    className="flex flex-wrap items-center justify-between gap-3 rounded border p-3"
                                >
                                    <div>
                                        <Link
                                            href={`/admin/invoices/${allocation.invoice.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {allocation.invoice.invoice_number}
                                        </Link>

                                        <p className="text-sm text-muted-foreground">
                                            {allocation.invoice.status}
                                        </p>
                                    </div>

                                    <div className="flex items-center gap-4">
                                        <strong>
                                            {payment.currency}{' '}
                                            {Number(allocation.amount).toFixed(
                                                2,
                                            )}
                                        </strong>

                                        <button
                                            type="button"
                                            onClick={() =>
                                                router.delete(
                                                    `/admin/payments/${payment.public_id}/allocations/${allocation.public_id}`,
                                                    {
                                                        preserveScroll: true,
                                                    },
                                                )
                                            }
                                            className="text-destructive underline"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-sm text-muted-foreground">
                            No allocations recorded.
                        </p>
                    )}
                </section>
            </div>
        </>
    );
}
