import AssetController from '@/actions/App/Http/Controllers/Admin/AssetController';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEvent, useState } from 'react';

type AssetRow = {
    public_id: string;
    asset_number: string;
    asset_tag: string;
    name: string;
    category: string;
    brand: string | null;
    model: string | null;
    serial_number: string | null;
    status: string;
    location: string | null;
    currency: string | null;
    purchase_cost: string | null;
};

type Supplier = {
    id: number;
    name: string;
};

type Project = {
    id: number;
    name: string;
};

type Props = {
    assets: AssetRow[];
    suppliers: Supplier[];
    projects: Project[];

    permissions: {
        create: boolean;
        export: boolean;
    };
};

export default function AssetIndex({
    assets,
    suppliers,
    projects,
    permissions,
}: Props) {
    const [showCreate, setShowCreate] = useState(false);

    const form = useForm({
        asset_tag: '',
        name: '',
        category: '',
        brand: '',
        model: '',
        serial_number: '',
        supplier_id: '',
        project_id: '',
        currency: 'LKR',
        purchase_cost: '',
        purchase_date: '',
        warranty_expires_at: '',
        location: '',
        notes: '',
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();

        form.post(AssetController.store().url, {
            preserveScroll: true,

            onSuccess: () => {
                form.reset();
                setShowCreate(false);
            },
        });
    };

    return (
        <>
            <Head title="Assets" />

            <div className="space-y-6 p-6">
                <header className="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">Assets</h1>

                        <p className="text-sm text-muted-foreground">
                            Company equipment, assignments, maintenance and
                            retirement.
                        </p>
                    </div>

                    <div className="flex gap-2">
                        {permissions.export && (
                            <a
                                href={AssetController.excel().url}
                                className="rounded border px-4 py-2 text-sm"
                            >
                                Export Excel
                            </a>
                        )}

                        {permissions.create && (
                            <button
                                type="button"
                                onClick={() => setShowCreate((value) => !value)}
                                className="rounded bg-primary px-4 py-2 text-primary-foreground"
                            >
                                New Asset
                            </button>
                        )}
                    </div>
                </header>

                {showCreate && permissions.create && (
                    <form
                        onSubmit={submit}
                        className="space-y-4 rounded border p-5"
                    >
                        <h2 className="font-semibold">Register Asset</h2>

                        <div className="grid gap-4 md:grid-cols-3">
                            <Input
                                label="Asset Tag"
                                value={form.data.asset_tag}
                                onChange={(value) =>
                                    form.setData('asset_tag', value)
                                }
                            />

                            <Input
                                label="Name"
                                value={form.data.name}
                                onChange={(value) =>
                                    form.setData('name', value)
                                }
                            />

                            <Input
                                label="Category"
                                value={form.data.category}
                                onChange={(value) =>
                                    form.setData('category', value)
                                }
                            />

                            <Input
                                label="Brand"
                                value={form.data.brand}
                                onChange={(value) =>
                                    form.setData('brand', value)
                                }
                            />

                            <Input
                                label="Model"
                                value={form.data.model}
                                onChange={(value) =>
                                    form.setData('model', value)
                                }
                            />

                            <Input
                                label="Serial Number"
                                value={form.data.serial_number}
                                onChange={(value) =>
                                    form.setData('serial_number', value)
                                }
                            />

                            <label className="space-y-1">
                                <span className="text-sm font-medium">
                                    Supplier
                                </span>

                                <select
                                    value={form.data.supplier_id}
                                    onChange={(event) =>
                                        form.setData(
                                            'supplier_id',
                                            event.target.value,
                                        )
                                    }
                                    className="w-full rounded border p-2"
                                >
                                    <option value="">None</option>

                                    {suppliers.map((supplier) => (
                                        <option
                                            key={supplier.id}
                                            value={supplier.id}
                                        >
                                            {supplier.name}
                                        </option>
                                    ))}
                                </select>
                            </label>

                            <label className="space-y-1">
                                <span className="text-sm font-medium">
                                    Project
                                </span>

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
                                    <option value="">None</option>

                                    {projects.map((project) => (
                                        <option
                                            key={project.id}
                                            value={project.id}
                                        >
                                            {project.name}
                                        </option>
                                    ))}
                                </select>
                            </label>

                            <Input
                                label="Location"
                                value={form.data.location}
                                onChange={(value) =>
                                    form.setData('location', value)
                                }
                            />

                            <Input
                                label="Currency"
                                value={form.data.currency}
                                onChange={(value) =>
                                    form.setData(
                                        'currency',
                                        value.toUpperCase(),
                                    )
                                }
                            />

                            <Input
                                label="Purchase Cost"
                                value={form.data.purchase_cost}
                                onChange={(value) =>
                                    form.setData('purchase_cost', value)
                                }
                            />

                            <DateInput
                                label="Purchase Date"
                                value={form.data.purchase_date}
                                onChange={(value) =>
                                    form.setData('purchase_date', value)
                                }
                            />

                            <DateInput
                                label="Warranty Expiry"
                                value={form.data.warranty_expires_at}
                                onChange={(value) =>
                                    form.setData('warranty_expires_at', value)
                                }
                            />
                        </div>

                        <textarea
                            value={form.data.notes}
                            onChange={(event) =>
                                form.setData('notes', event.target.value)
                            }
                            placeholder="Notes"
                            className="min-h-24 w-full rounded border p-2"
                        />

                        <button
                            disabled={form.processing}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            Register Asset
                        </button>
                    </form>
                )}

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="border-b bg-muted/40">
                            <tr>
                                <Th>Asset</Th>
                                <Th>Tag</Th>
                                <Th>Name</Th>
                                <Th>Category</Th>
                                <Th>Serial</Th>
                                <Th>Location</Th>
                                <Th>Status</Th>
                                <Th>Action</Th>
                            </tr>
                        </thead>

                        <tbody>
                            {assets.map((asset) => (
                                <tr
                                    key={asset.public_id}
                                    className="border-b last:border-0"
                                >
                                    <Td>{asset.asset_number}</Td>

                                    <Td>{asset.asset_tag}</Td>

                                    <Td>{asset.name}</Td>

                                    <Td>{asset.category}</Td>

                                    <Td>{asset.serial_number ?? '-'}</Td>

                                    <Td>{asset.location ?? '-'}</Td>

                                    <Td>
                                        <StatusBadge value={asset.status} />
                                    </Td>

                                    <Td>
                                        <Link
                                            href={
                                                AssetController.show(
                                                    asset.public_id,
                                                ).url
                                            }
                                            className="underline"
                                        >
                                            View
                                        </Link>
                                    </Td>
                                </tr>
                            ))}

                            {assets.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={8}
                                        className="p-8 text-center text-muted-foreground"
                                    >
                                        No assets registered.
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

function Input({
    label,
    value,
    onChange,
}: {
    label: string;
    value: string;
    onChange: (value: string) => void;
}) {
    return (
        <label className="space-y-1">
            <span className="text-sm font-medium">{label}</span>

            <input
                value={value}
                onChange={(event) => onChange(event.target.value)}
                className="w-full rounded border p-2"
            />
        </label>
    );
}

function DateInput({
    label,
    value,
    onChange,
}: {
    label: string;
    value: string;
    onChange: (value: string) => void;
}) {
    return (
        <label className="space-y-1">
            <span className="text-sm font-medium">{label}</span>

            <input
                type="date"
                value={value}
                onChange={(event) => onChange(event.target.value)}
                className="w-full rounded border p-2"
            />
        </label>
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
