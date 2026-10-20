import ProcurementRequestController from '@/actions/App/Http/Controllers/Admin/ProcurementRequestController';
import ProcurementReviewController from '@/actions/App/Http/Controllers/Admin/ProcurementReviewController';
import PurchaseOrderController from '@/actions/App/Http/Controllers/Admin/PurchaseOrderController';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type Item = {
    public_id: string;
    description: string;
    quantity: string;
    unit: string;
    estimated_unit_cost: string;
    estimated_total: string;
    notes: string | null;
};

type Supplier = {
    id: number;
    public_id: string;
    name: string;
};

type ProcurementRequest = {
    public_id: string;
    request_number: string;
    title: string;
    business_reason: string;
    priority: string;
    status: string;
    currency: string;
    estimated_total: string;

    requester: {
        name: string;
    };

    reviewer: {
        name: string;
    } | null;

    project: {
        public_id: string;
        name: string;
    } | null;

    submitted_at: string | null;
    reviewed_at: string | null;
    review_notes: string | null;

    items: Item[];

    purchase_orders: {
        public_id: string;
        purchase_order_number: string;
        status: string;
    }[];
};

type Permissions = {
    submit: boolean;
    review: boolean;
    create_purchase_order: boolean;
};

type Props = {
    request: ProcurementRequest;
    suppliers: Supplier[];
    permissions: Permissions;
};

export default function ProcurementShow({
    request,
    suppliers,
    permissions,
}: Props) {
    const reviewForm = useForm({
        decision: 'approved',
        review_notes: '',
    });

    const orderForm = useForm({
        supplier_id: '',
        currency: request.currency,
        order_date: new Date().toISOString().slice(0, 10),
        expected_delivery_date: '',
        tax_amount: '0.00',
        supplier_reference: '',
        notes: '',
        procurement_request_id: request.public_id,
    });

    const submitRequest = () => {
        router.post(
            ProcurementRequestController.submit(request.public_id).url,
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const review = (event: FormEvent) => {
        event.preventDefault();

        reviewForm.post(
            ProcurementReviewController.store(request.public_id).url,
            {
                preserveScroll: true,
            },
        );
    };

    const createOrder = (event: FormEvent) => {
        event.preventDefault();

        orderForm.post(PurchaseOrderController.store().url, {
            preserveScroll: true,
        });
    };

    return (
        <>
            <Head title={request.request_number} />

            <div className="space-y-6 p-6">
                <header>
                    <Link
                        href={ProcurementRequestController.index().url}
                        className="text-sm underline"
                    >
                        ← Procurement Requests
                    </Link>

                    <div className="mt-3 flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <h1 className="text-2xl font-semibold">
                                {request.request_number}
                            </h1>

                            <p className="text-muted-foreground">
                                {request.title}
                            </p>
                        </div>

                        <StatusBadge value={request.status} />
                    </div>
                </header>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-3">
                    <Info label="Requester" value={request.requester.name} />

                    <Info
                        label="Project"
                        value={request.project?.name ?? 'General / Internal'}
                    />

                    <Info label="Priority" value={request.priority} />

                    <Info label="Currency" value={request.currency} />

                    <Info
                        label="Estimated Total"
                        value={`${request.currency} ${request.estimated_total}`}
                    />

                    <Info
                        label="Reviewer"
                        value={request.reviewer?.name ?? '-'}
                    />
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-2 font-semibold">Business Reason</h2>

                    <p className="text-sm whitespace-pre-wrap">
                        {request.business_reason}
                    </p>
                </section>

                <section className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="border-b bg-muted/40">
                            <tr>
                                <Th>Description</Th>
                                <Th>Qty</Th>
                                <Th>Unit</Th>
                                <Th>Unit Cost</Th>
                                <Th>Total</Th>
                            </tr>
                        </thead>

                        <tbody>
                            {request.items.map((item) => (
                                <tr
                                    key={item.public_id}
                                    className="border-b last:border-0"
                                >
                                    <Td>{item.description}</Td>
                                    <Td>{item.quantity}</Td>
                                    <Td>{item.unit}</Td>
                                    <Td>{item.estimated_unit_cost}</Td>
                                    <Td>{item.estimated_total}</Td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </section>

                {request.review_notes && (
                    <section className="rounded border p-5">
                        <h2 className="font-semibold">Review Notes</h2>

                        <p className="mt-2 text-sm whitespace-pre-wrap">
                            {request.review_notes}
                        </p>
                    </section>
                )}

                {permissions.submit && request.status === 'draft' && (
                    <section className="rounded border p-5">
                        <h2 className="font-semibold">Submit Request</h2>

                        <p className="my-3 text-sm text-muted-foreground">
                            Submission sends this request for internal approval.
                        </p>

                        <button
                            type="button"
                            onClick={submitRequest}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            Submit
                        </button>
                    </section>
                )}

                {permissions.review && request.status === 'submitted' && (
                    <form
                        onSubmit={review}
                        className="space-y-4 rounded border p-5"
                    >
                        <h2 className="font-semibold">Review Request</h2>

                        <select
                            value={reviewForm.data.decision}
                            onChange={(event) =>
                                reviewForm.setData(
                                    'decision',
                                    event.target.value,
                                )
                            }
                            className="w-full rounded border p-2"
                        >
                            <option value="approved">Approve</option>

                            <option value="rejected">Reject</option>
                        </select>

                        <textarea
                            value={reviewForm.data.review_notes}
                            onChange={(event) =>
                                reviewForm.setData(
                                    'review_notes',
                                    event.target.value,
                                )
                            }
                            placeholder="Review notes"
                            className="min-h-24 w-full rounded border p-2"
                        />

                        {reviewForm.errors.review_notes && (
                            <p className="text-sm text-red-600">
                                {reviewForm.errors.review_notes}
                            </p>
                        )}

                        <button
                            disabled={reviewForm.processing}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            Save Decision
                        </button>
                    </form>
                )}

                {permissions.create_purchase_order &&
                    request.status === 'approved' &&
                    request.purchase_orders.length === 0 && (
                        <form
                            onSubmit={createOrder}
                            className="space-y-4 rounded border p-5"
                        >
                            <h2 className="font-semibold">
                                Create Purchase Order
                            </h2>

                            <div className="grid gap-4 md:grid-cols-2">
                                <select
                                    value={orderForm.data.supplier_id}
                                    onChange={(event) =>
                                        orderForm.setData(
                                            'supplier_id',
                                            event.target.value,
                                        )
                                    }
                                    className="rounded border p-2"
                                >
                                    <option value="">Select supplier</option>

                                    {suppliers.map((supplier) => (
                                        <option
                                            key={supplier.id}
                                            value={supplier.id}
                                        >
                                            {supplier.name}
                                        </option>
                                    ))}
                                </select>

                                <input
                                    value={orderForm.data.currency}
                                    readOnly
                                    className="rounded border bg-muted p-2"
                                />

                                <input
                                    type="date"
                                    value={orderForm.data.order_date}
                                    onChange={(event) =>
                                        orderForm.setData(
                                            'order_date',
                                            event.target.value,
                                        )
                                    }
                                    className="rounded border p-2"
                                />

                                <input
                                    type="date"
                                    value={
                                        orderForm.data.expected_delivery_date
                                    }
                                    onChange={(event) =>
                                        orderForm.setData(
                                            'expected_delivery_date',
                                            event.target.value,
                                        )
                                    }
                                    className="rounded border p-2"
                                />

                                <input
                                    value={orderForm.data.tax_amount}
                                    onChange={(event) =>
                                        orderForm.setData(
                                            'tax_amount',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Tax amount"
                                    className="rounded border p-2"
                                />

                                <input
                                    value={orderForm.data.supplier_reference}
                                    onChange={(event) =>
                                        orderForm.setData(
                                            'supplier_reference',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Supplier reference"
                                    className="rounded border p-2"
                                />
                            </div>

                            <textarea
                                value={orderForm.data.notes}
                                onChange={(event) =>
                                    orderForm.setData(
                                        'notes',
                                        event.target.value,
                                    )
                                }
                                placeholder="Notes"
                                className="min-h-24 w-full rounded border p-2"
                            />

                            <button
                                disabled={orderForm.processing}
                                className="rounded bg-primary px-4 py-2 text-primary-foreground"
                            >
                                Create Purchase Order
                            </button>
                        </form>
                    )}

                {request.purchase_orders.length > 0 && (
                    <section className="rounded border p-5">
                        <h2 className="mb-3 font-semibold">Purchase Orders</h2>

                        <div className="space-y-2">
                            {request.purchase_orders.map((order) => (
                                <Link
                                    key={order.public_id}
                                    href={
                                        PurchaseOrderController.show(
                                            order.public_id,
                                        ).url
                                    }
                                    className="flex justify-between rounded border p-3"
                                >
                                    <span>{order.purchase_order_number}</span>

                                    <StatusBadge value={order.status} />
                                </Link>
                            ))}
                        </div>
                    </section>
                )}
            </div>
        </>
    );
}

function Info({ label, value }: { label: string; value: string }) {
    return (
        <div>
            <p className="text-sm text-muted-foreground">{label}</p>
            <p className="font-medium">{value}</p>
        </div>
    );
}

function Th({ children }: { children: React.ReactNode }) {
    return <th className="px-4 py-3 text-left">{children}</th>;
}

function Td({ children }: { children: React.ReactNode }) {
    return <td className="px-4 py-3">{children}</td>;
}

function StatusBadge({ value }: { value: string }) {
    return (
        <span className="rounded-full border px-3 py-1 text-xs capitalize">
            {value.replaceAll('_', ' ')}
        </span>
    );
}
