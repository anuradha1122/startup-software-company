import ProcurementRequestController from '@/actions/App/Http/Controllers/Admin/ProcurementRequestController';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEvent, useState } from 'react';

type ProjectOption = {
    id: number;
    public_id: string;
    name: string;
};

type ProcurementRequestRow = {
    public_id: string;
    request_number: string;
    title: string;
    priority: string;
    status: string;
    currency: string;
    estimated_total: string;
    requester: {
        name: string;
    };
    project: {
        public_id: string;
        name: string;
    } | null;
    submitted_at: string | null;
};

type Permissions = {
    create: boolean;
    export: boolean;
};

type Props = {
    requests: ProcurementRequestRow[];
    projects: ProjectOption[];
    permissions: Permissions;
};

type ItemInput = {
    description: string;
    quantity: string;
    unit: string;
    estimated_unit_cost: string;
    notes: string;
};

type ProcurementForm = {
    project_id: string;
    title: string;
    business_reason: string;
    priority: string;
    currency: string;
    items: ItemInput[];
};

const emptyItem = (): ItemInput => ({
    description: '',
    quantity: '1.00',
    unit: 'unit',
    estimated_unit_cost: '0.00',
    notes: '',
});

export default function ProcurementIndex({
    requests,
    projects,
    permissions,
}: Props) {
    const [showCreate, setShowCreate] = useState(false);

    const form = useForm<ProcurementForm>({
        project_id: '',
        title: '',
        business_reason: '',
        priority: 'medium',
        currency: 'LKR',
        items: [emptyItem()],
    });

    const addItem = () => {
        form.setData('items', [...form.data.items, emptyItem()]);
    };

    const removeItem = (index: number) => {
        if (form.data.items.length === 1) {
            return;
        }

        form.setData(
            'items',
            form.data.items.filter((_, itemIndex) => itemIndex !== index),
        );
    };

    const updateItem = (
        index: number,
        field: keyof ItemInput,
        value: string,
    ) => {
        const items = form.data.items.map((item, itemIndex) =>
            itemIndex === index
                ? {
                      ...item,
                      [field]: value,
                  }
                : item,
        );

        form.setData('items', items);
    };

    const submit = (event: FormEvent) => {
        event.preventDefault();

        form.post(ProcurementRequestController.store().url, {
            preserveScroll: true,

            onSuccess: () => {
                form.reset();
                form.setData('items', [emptyItem()]);
                setShowCreate(false);
            },
        });
    };

    return (
        <>
            <Head title="Procurement Requests" />

            <div className="space-y-6 p-6">
                <header className="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Procurement Requests
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            Create and manage internal procurement requests.
                        </p>
                    </div>

                    <div className="flex gap-2">
                        {permissions.export && (
                            <a
                                href={ProcurementRequestController.excel().url}
                                className="rounded border px-4 py-2 text-sm"
                            >
                                Export Excel
                            </a>
                        )}

                        {permissions.create && (
                            <button
                                type="button"
                                onClick={() => setShowCreate((value) => !value)}
                                className="rounded bg-primary px-4 py-2 text-sm text-primary-foreground"
                            >
                                New Request
                            </button>
                        )}
                    </div>
                </header>

                {showCreate && permissions.create && (
                    <form
                        onSubmit={submit}
                        className="space-y-5 rounded border p-5"
                    >
                        <h2 className="text-lg font-semibold">
                            Create Procurement Request
                        </h2>

                        <div className="grid gap-4 md:grid-cols-2">
                            <Field label="Title" error={form.errors.title}>
                                <input
                                    value={form.data.title}
                                    onChange={(event) =>
                                        form.setData(
                                            'title',
                                            event.target.value,
                                        )
                                    }
                                    className="w-full rounded border p-2"
                                />
                            </Field>

                            <Field
                                label="Project"
                                error={form.errors.project_id}
                            >
                                <select
                                    value={form.data.project_id}
                                    onChange={(event) =>
                                        form.setData(
                                            'project_id',
                                            event.target.value,
                                        )
                                    }
                                    className="w-full rounded border p-2"
                                >
                                    <option value="">General / Internal</option>

                                    {projects.map((project) => (
                                        <option
                                            key={project.id}
                                            value={project.id}
                                        >
                                            {project.name}
                                        </option>
                                    ))}
                                </select>
                            </Field>

                            <Field
                                label="Priority"
                                error={form.errors.priority}
                            >
                                <select
                                    value={form.data.priority}
                                    onChange={(event) =>
                                        form.setData(
                                            'priority',
                                            event.target.value,
                                        )
                                    }
                                    className="w-full rounded border p-2"
                                >
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </Field>

                            <Field
                                label="Currency"
                                error={form.errors.currency}
                            >
                                <input
                                    value={form.data.currency}
                                    maxLength={3}
                                    onChange={(event) =>
                                        form.setData(
                                            'currency',
                                            event.target.value.toUpperCase(),
                                        )
                                    }
                                    className="w-full rounded border p-2"
                                />
                            </Field>
                        </div>

                        <Field
                            label="Business Reason"
                            error={form.errors.business_reason}
                        >
                            <textarea
                                value={form.data.business_reason}
                                onChange={(event) =>
                                    form.setData(
                                        'business_reason',
                                        event.target.value,
                                    )
                                }
                                className="min-h-28 w-full rounded border p-2"
                            />
                        </Field>

                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <h3 className="font-medium">Items</h3>

                                <button
                                    type="button"
                                    onClick={addItem}
                                    className="rounded border px-3 py-1 text-sm"
                                >
                                    Add Item
                                </button>
                            </div>

                            {form.data.items.map((item, index) => (
                                <div
                                    key={index}
                                    className="grid gap-3 rounded border p-4 md:grid-cols-6"
                                >
                                    <input
                                        placeholder="Description"
                                        value={item.description}
                                        onChange={(event) =>
                                            updateItem(
                                                index,
                                                'description',
                                                event.target.value,
                                            )
                                        }
                                        className="rounded border p-2 md:col-span-2"
                                    />

                                    <input
                                        placeholder="Quantity"
                                        value={item.quantity}
                                        onChange={(event) =>
                                            updateItem(
                                                index,
                                                'quantity',
                                                event.target.value,
                                            )
                                        }
                                        className="rounded border p-2"
                                    />

                                    <input
                                        placeholder="Unit"
                                        value={item.unit}
                                        onChange={(event) =>
                                            updateItem(
                                                index,
                                                'unit',
                                                event.target.value,
                                            )
                                        }
                                        className="rounded border p-2"
                                    />

                                    <input
                                        placeholder="Estimated unit cost"
                                        value={item.estimated_unit_cost}
                                        onChange={(event) =>
                                            updateItem(
                                                index,
                                                'estimated_unit_cost',
                                                event.target.value,
                                            )
                                        }
                                        className="rounded border p-2"
                                    />

                                    <button
                                        type="button"
                                        onClick={() => removeItem(index)}
                                        className="rounded border px-3 py-2"
                                    >
                                        Remove
                                    </button>

                                    <textarea
                                        placeholder="Notes"
                                        value={item.notes}
                                        onChange={(event) =>
                                            updateItem(
                                                index,
                                                'notes',
                                                event.target.value,
                                            )
                                        }
                                        className="rounded border p-2 md:col-span-6"
                                    />
                                </div>
                            ))}
                        </div>

                        <button
                            type="submit"
                            disabled={form.processing}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground disabled:opacity-50"
                        >
                            {form.processing ? 'Saving...' : 'Create Request'}
                        </button>
                    </form>
                )}

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="border-b bg-muted/40">
                            <tr>
                                <Th>Request</Th>
                                <Th>Title</Th>
                                <Th>Project</Th>
                                <Th>Priority</Th>
                                <Th>Status</Th>
                                <Th>Total</Th>
                                <Th>Requester</Th>
                                <Th>Action</Th>
                            </tr>
                        </thead>

                        <tbody>
                            {requests.map((request) => (
                                <tr
                                    key={request.public_id}
                                    className="border-b last:border-0"
                                >
                                    <Td>{request.request_number}</Td>

                                    <Td>{request.title}</Td>

                                    <Td>{request.project?.name ?? '-'}</Td>

                                    <Td>
                                        <StatusBadge value={request.priority} />
                                    </Td>

                                    <Td>
                                        <StatusBadge value={request.status} />
                                    </Td>

                                    <Td>
                                        {request.currency}{' '}
                                        {request.estimated_total}
                                    </Td>

                                    <Td>{request.requester.name}</Td>

                                    <Td>
                                        <Link
                                            href={
                                                ProcurementRequestController.show(
                                                    request.public_id,
                                                ).url
                                            }
                                            className="underline"
                                        >
                                            View
                                        </Link>
                                    </Td>
                                </tr>
                            ))}

                            {requests.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={8}
                                        className="p-8 text-center text-muted-foreground"
                                    >
                                        No procurement requests found.
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

function Field({
    label,
    error,
    children,
}: {
    label: string;
    error?: string;
    children: React.ReactNode;
}) {
    return (
        <label className="space-y-1">
            <span className="text-sm font-medium">{label}</span>

            {children}

            {error && (
                <span className="block text-sm text-red-600">{error}</span>
            )}
        </label>
    );
}

function Th({ children }: { children: React.ReactNode }) {
    return <th className="px-4 py-3 text-left font-medium">{children}</th>;
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
