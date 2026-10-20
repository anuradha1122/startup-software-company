import GoodsReceiptController from '@/actions/App/Http/Controllers/Admin/GoodsReceiptController';
import PurchaseOrderController from '@/actions/App/Http/Controllers/Admin/PurchaseOrderController';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type PurchaseOrderItem = {
    id: number;
    public_id: string;
    description: string;
    quantity: string;
    unit: string;
    unit_cost: string;
    line_total: string;
    received_quantity: string;
    asset_trackable: boolean;
};

type Receipt = {
    public_id: string;
    receipt_number: string;
    received_date: string;

    receiver: {
        name: string;
    };

    items: {
        public_id: string;
        quantity_received: string;
        quantity_rejected: string;
    }[];
};

type PurchaseOrder = {
    public_id: string;
    purchase_order_number: string;
    currency: string;
    status: string;
    order_date: string;
    expected_delivery_date: string | null;
    subtotal: string;
    tax_amount: string;
    total: string;
    supplier_reference: string | null;
    notes: string | null;

    supplier: {
        name: string;
    };

    request: {
        public_id: string;
        request_number: string;
    } | null;

    project: {
        name: string;
    } | null;

    items: PurchaseOrderItem[];

    receipts: Receipt[];
};

type Props = {
    order: PurchaseOrder;

    permissions: {
        issue: boolean;
        cancel: boolean;
        receive: boolean;
        pdf: boolean;
    };
};

export default function PurchaseOrderShow({ order, permissions }: Props) {
    const receiptForm = useForm({
        received_date: new Date().toISOString().slice(0, 10),

        delivery_reference: '',
        notes: '',

        items: order.items.map((item) => ({
            purchase_order_item_id: item.id,

            quantity_received: '0.00',

            quantity_rejected: '0.00',

            condition_notes: '',
        })),
    });

    const issue = () => {
        router.post(
            PurchaseOrderController.issue(order.public_id).url,
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const cancel = () => {
        if (!window.confirm('Cancel this purchase order?')) {
            return;
        }

        router.post(
            PurchaseOrderController.cancel(order.public_id).url,
            {},
            {
                preserveScroll: true,
            },
        );
    };

    const updateReceiptItem = (
        index: number,
        field: 'quantity_received' | 'quantity_rejected' | 'condition_notes',
        value: string,
    ) => {
        const items = receiptForm.data.items.map((item, itemIndex) =>
            itemIndex === index
                ? {
                      ...item,
                      [field]: value,
                  }
                : item,
        );

        receiptForm.setData('items', items);
    };

    const receive = (event: FormEvent) => {
        event.preventDefault();

        receiptForm.post(GoodsReceiptController.store(order.public_id).url, {
            preserveScroll: true,
        });
    };

    return (
        <>
            <Head title={order.purchase_order_number} />

            <div className="space-y-6 p-6">
                <header>
                    <Link
                        href={PurchaseOrderController.index().url}
                        className="text-sm underline"
                    >
                        ← Purchase Orders
                    </Link>

                    <div className="mt-3 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h1 className="text-2xl font-semibold">
                                {order.purchase_order_number}
                            </h1>

                            <p className="text-muted-foreground">
                                {order.supplier.name}
                            </p>
                        </div>

                        <StatusBadge value={order.status} />
                    </div>
                </header>

                <div className="flex flex-wrap gap-2">
                    {permissions.pdf && (
                        <a
                            href={
                                PurchaseOrderController.pdf(order.public_id).url
                            }
                            target="_blank"
                            rel="noreferrer"
                            className="rounded border px-4 py-2 text-sm"
                        >
                            PDF
                        </a>
                    )}

                    {permissions.issue && order.status === 'draft' && (
                        <button
                            type="button"
                            onClick={issue}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            Issue PO
                        </button>
                    )}

                    {permissions.cancel &&
                        ['draft', 'issued'].includes(order.status) && (
                            <button
                                type="button"
                                onClick={cancel}
                                className="rounded border px-4 py-2"
                            >
                                Cancel PO
                            </button>
                        )}
                </div>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-3">
                    <Info label="Supplier" value={order.supplier.name} />

                    <Info label="Project" value={order.project?.name ?? '-'} />

                    <Info label="Order Date" value={order.order_date} />

                    <Info
                        label="Expected Delivery"
                        value={order.expected_delivery_date ?? '-'}
                    />

                    <Info
                        label="Supplier Reference"
                        value={order.supplier_reference ?? '-'}
                    />

                    <Info label="Currency" value={order.currency} />
                </section>

                <section className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="border-b bg-muted/40">
                            <tr>
                                <Th>Description</Th>
                                <Th>Ordered</Th>
                                <Th>Received</Th>
                                <Th>Unit</Th>
                                <Th>Unit Cost</Th>
                                <Th>Line Total</Th>
                            </tr>
                        </thead>

                        <tbody>
                            {order.items.map((item) => (
                                <tr
                                    key={item.public_id}
                                    className="border-b last:border-0"
                                >
                                    <Td>{item.description}</Td>

                                    <Td>{item.quantity}</Td>

                                    <Td>{item.received_quantity}</Td>

                                    <Td>{item.unit}</Td>

                                    <Td>
                                        {order.currency} {item.unit_cost}
                                    </Td>

                                    <Td>
                                        {order.currency} {item.line_total}
                                    </Td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </section>

                <section className="rounded border p-5">
                    <div className="grid gap-3 md:grid-cols-3">
                        <Info
                            label="Subtotal"
                            value={`${order.currency} ${order.subtotal}`}
                        />

                        <Info
                            label="Tax"
                            value={`${order.currency} ${order.tax_amount}`}
                        />

                        <Info
                            label="Total"
                            value={`${order.currency} ${order.total}`}
                        />
                    </div>
                </section>

                {permissions.receive &&
                    ['issued', 'partially_received'].includes(order.status) && (
                        <form
                            onSubmit={receive}
                            className="space-y-4 rounded border p-5"
                        >
                            <h2 className="font-semibold">
                                Record Goods Receipt
                            </h2>

                            <div className="grid gap-4 md:grid-cols-2">
                                <input
                                    type="date"
                                    value={receiptForm.data.received_date}
                                    onChange={(event) =>
                                        receiptForm.setData(
                                            'received_date',
                                            event.target.value,
                                        )
                                    }
                                    className="rounded border p-2"
                                />

                                <input
                                    value={receiptForm.data.delivery_reference}
                                    onChange={(event) =>
                                        receiptForm.setData(
                                            'delivery_reference',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Delivery reference"
                                    className="rounded border p-2"
                                />
                            </div>

                            {order.items.map((item, index) => (
                                <div
                                    key={item.public_id}
                                    className="grid gap-3 rounded border p-4 md:grid-cols-4"
                                >
                                    <div>
                                        <p className="font-medium">
                                            {item.description}
                                        </p>

                                        <p className="text-xs text-muted-foreground">
                                            Ordered: {item.quantity} • Received:{' '}
                                            {item.received_quantity}
                                        </p>
                                    </div>

                                    <input
                                        value={
                                            receiptForm.data.items[index]
                                                .quantity_received
                                        }
                                        onChange={(event) =>
                                            updateReceiptItem(
                                                index,
                                                'quantity_received',
                                                event.target.value,
                                            )
                                        }
                                        placeholder="Received"
                                        className="rounded border p-2"
                                    />

                                    <input
                                        value={
                                            receiptForm.data.items[index]
                                                .quantity_rejected
                                        }
                                        onChange={(event) =>
                                            updateReceiptItem(
                                                index,
                                                'quantity_rejected',
                                                event.target.value,
                                            )
                                        }
                                        placeholder="Rejected"
                                        className="rounded border p-2"
                                    />

                                    <input
                                        value={
                                            receiptForm.data.items[index]
                                                .condition_notes
                                        }
                                        onChange={(event) =>
                                            updateReceiptItem(
                                                index,
                                                'condition_notes',
                                                event.target.value,
                                            )
                                        }
                                        placeholder="Condition notes"
                                        className="rounded border p-2"
                                    />
                                </div>
                            ))}

                            <textarea
                                value={receiptForm.data.notes}
                                onChange={(event) =>
                                    receiptForm.setData(
                                        'notes',
                                        event.target.value,
                                    )
                                }
                                placeholder="Receipt notes"
                                className="min-h-24 w-full rounded border p-2"
                            />

                            <button
                                disabled={receiptForm.processing}
                                className="rounded bg-primary px-4 py-2 text-primary-foreground"
                            >
                                Save Receipt
                            </button>
                        </form>
                    )}

                <section className="rounded border p-5">
                    <h2 className="mb-3 font-semibold">Goods Receipts</h2>

                    {order.receipts.length === 0 ? (
                        <p className="text-sm text-muted-foreground">
                            No goods receipts.
                        </p>
                    ) : (
                        <div className="space-y-2">
                            {order.receipts.map((receipt) => (
                                <div
                                    key={receipt.public_id}
                                    className="flex justify-between rounded border p-3 text-sm"
                                >
                                    <span>{receipt.receipt_number}</span>

                                    <span>{receipt.received_date}</span>

                                    <span>{receipt.receiver.name}</span>
                                </div>
                            ))}
                        </div>
                    )}
                </section>
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
