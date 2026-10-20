import PurchaseOrderController from '@/actions/App/Http/Controllers/Admin/PurchaseOrderController';
import { Head, Link } from '@inertiajs/react';

type PurchaseOrderRow = {
    public_id: string;
    purchase_order_number: string;
    currency: string;
    total: string;
    order_date: string;
    expected_delivery_date: string | null;
    status: string;

    supplier: {
        name: string;
    };

    project: {
        name: string;
    } | null;

    request: {
        request_number: string;
        public_id: string;
    } | null;
};

type Props = {
    orders: PurchaseOrderRow[];

    permissions: {
        export: boolean;
    };
};

export default function PurchaseOrderIndex({ orders, permissions }: Props) {
    return (
        <>
            <Head title="Purchase Orders" />

            <div className="space-y-6 p-6">
                <header className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Purchase Orders
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            Supplier orders generated from approved procurement
                            requests.
                        </p>
                    </div>

                    {permissions.export && (
                        <a
                            href={PurchaseOrderController.excel().url}
                            className="rounded border px-4 py-2 text-sm"
                        >
                            Export Excel
                        </a>
                    )}
                </header>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="border-b bg-muted/40">
                            <tr>
                                <Th>PO Number</Th>
                                <Th>Supplier</Th>
                                <Th>Request</Th>
                                <Th>Project</Th>
                                <Th>Order Date</Th>
                                <Th>Expected</Th>
                                <Th>Total</Th>
                                <Th>Status</Th>
                                <Th>Action</Th>
                            </tr>
                        </thead>

                        <tbody>
                            {orders.map((order) => (
                                <tr
                                    key={order.public_id}
                                    className="border-b last:border-0"
                                >
                                    <Td>{order.purchase_order_number}</Td>

                                    <Td>{order.supplier.name}</Td>

                                    <Td>
                                        {order.request?.request_number ?? '-'}
                                    </Td>

                                    <Td>{order.project?.name ?? '-'}</Td>

                                    <Td>{order.order_date}</Td>

                                    <Td>
                                        {order.expected_delivery_date ?? '-'}
                                    </Td>

                                    <Td>
                                        {order.currency} {order.total}
                                    </Td>

                                    <Td>
                                        <StatusBadge value={order.status} />
                                    </Td>

                                    <Td>
                                        <Link
                                            href={
                                                PurchaseOrderController.show(
                                                    order.public_id,
                                                ).url
                                            }
                                            className="underline"
                                        >
                                            View
                                        </Link>
                                    </Td>
                                </tr>
                            ))}

                            {orders.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={9}
                                        className="p-8 text-center text-muted-foreground"
                                    >
                                        No purchase orders found.
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

function Th({ children }: { children: React.ReactNode }) {
    return <th className="px-4 py-3 text-left">{children}</th>;
}

function Td({ children }: { children: React.ReactNode }) {
    return <td className="px-4 py-3">{children}</td>;
}

function StatusBadge({ value }: { value: string }) {
    return (
        <span className="rounded-full border px-2 py-1 text-xs capitalize">
            {value.replaceAll('_', ' ')}
        </span>
    );
}
