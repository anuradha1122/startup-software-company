import { Head, Link, useForm } from '@inertiajs/react';

type Client = {
    id: number;
    public_id: string;
    name: string;
    default_currency: string;
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
    client: {
        name: string;
    };
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedPayments = {
    data: Payment[];
    links: PaginationLink[];
    last_page: number;
};

type Props = {
    payments: PaginatedPayments;
    clients: Client[];
};

type PaymentForm = {
    client_id: string;
    payment_date: string;
    currency: string;
    amount: number | '';
    method: 'bank_transfer' | 'cash' | 'card' | 'cheque' | 'online' | 'other';
    reference_number: string;
    notes: string;
};

export default function PaymentIndex({ payments, clients }: Props) {
    const form = useForm<PaymentForm>({
        client_id: '',
        payment_date: '',
        currency: 'LKR',
        amount: '',
        method: 'bank_transfer',
        reference_number: '',
        notes: '',
    });

    const handleClientChange = (clientId: string) => {
        form.setData('client_id', clientId);

        const client = clients.find((item) => String(item.id) === clientId);

        if (client) {
            form.setData('currency', client.default_currency || 'LKR');
        }
    };

    return (
        <>
            <Head title="Payments" />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">Payments</h1>

                        <p className="text-sm text-muted-foreground">
                            Record client payments and allocate them to
                            invoices.
                        </p>
                    </div>

                    <a
                        href="/admin/payments/export/excel"
                        className="rounded border px-4 py-2"
                    >
                        Export Excel
                    </a>
                </div>

                <form
                    className="grid gap-3 rounded border p-5 md:grid-cols-3"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/admin/payments');
                    }}
                >
                    <select
                        value={form.data.client_id}
                        onChange={(event) =>
                            handleClientChange(event.target.value)
                        }
                        className="rounded border p-2"
                    >
                        <option value="">Select client</option>

                        {clients.map((client) => (
                            <option key={client.id} value={client.id}>
                                {client.name}
                            </option>
                        ))}
                    </select>

                    <input
                        type="date"
                        value={form.data.payment_date}
                        onChange={(event) =>
                            form.setData('payment_date', event.target.value)
                        }
                        className="rounded border p-2"
                    />

                    <input
                        maxLength={3}
                        value={form.data.currency}
                        onChange={(event) =>
                            form.setData(
                                'currency',
                                event.target.value.toUpperCase(),
                            )
                        }
                        className="rounded border p-2"
                    />

                    <input
                        type="number"
                        min="0.01"
                        step="0.01"
                        placeholder="Amount"
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

                    <select
                        value={form.data.method}
                        onChange={(event) =>
                            form.setData(
                                'method',
                                event.target.value as PaymentForm['method'],
                            )
                        }
                        className="rounded border p-2"
                    >
                        <option value="bank_transfer">Bank Transfer</option>

                        <option value="cash">Cash</option>

                        <option value="card">Card</option>

                        <option value="cheque">Cheque</option>

                        <option value="online">Online</option>

                        <option value="other">Other</option>
                    </select>

                    <input
                        placeholder="Reference number"
                        value={form.data.reference_number}
                        onChange={(event) =>
                            form.setData('reference_number', event.target.value)
                        }
                        className="rounded border p-2"
                    />

                    <textarea
                        placeholder="Notes"
                        value={form.data.notes}
                        onChange={(event) =>
                            form.setData('notes', event.target.value)
                        }
                        className="min-h-24 rounded border p-2 md:col-span-2"
                    />

                    <button
                        disabled={form.processing}
                        className="rounded bg-primary px-4 py-2 text-primary-foreground disabled:opacity-50"
                    >
                        Record Payment
                    </button>
                </form>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Receipt</th>

                                <th className="p-3 text-left">Client</th>

                                <th className="p-3 text-left">Date</th>

                                <th className="p-3 text-left">Method</th>

                                <th className="p-3 text-left">Amount</th>

                                <th className="p-3 text-left">Allocated</th>

                                <th className="p-3 text-left">Unallocated</th>
                            </tr>
                        </thead>

                        <tbody>
                            {payments.data.map((payment) => (
                                <tr
                                    key={payment.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/payments/${payment.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {payment.payment_number}
                                        </Link>
                                    </td>

                                    <td className="p-3">
                                        {payment.client.name}
                                    </td>

                                    <td className="p-3">
                                        {payment.payment_date}
                                    </td>

                                    <td className="p-3">{payment.method}</td>

                                    <td className="p-3">
                                        {payment.currency}{' '}
                                        {Number(payment.amount).toFixed(2)}
                                    </td>

                                    <td className="p-3">
                                        {payment.currency}{' '}
                                        {Number(
                                            payment.allocated_amount,
                                        ).toFixed(2)}
                                    </td>

                                    <td className="p-3">
                                        {payment.currency}{' '}
                                        {Number(
                                            payment.unallocated_amount,
                                        ).toFixed(2)}
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
