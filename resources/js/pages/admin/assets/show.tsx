import AssetAssignmentController from '@/actions/App/Http/Controllers/Admin/AssetAssignmentController';
import AssetController from '@/actions/App/Http/Controllers/Admin/AssetController';
import AssetMaintenanceController from '@/actions/App/Http/Controllers/Admin/AssetMaintenanceController';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type UserOption = {
    id: number;
    name: string;
    email: string;
};

type ProjectOption = {
    id: number;
    name: string;
};

type Assignment = {
    public_id: string;
    assigned_date: string;
    expected_return_date: string | null;
    returned_date: string | null;
    status: string;
    assignment_notes: string | null;
    return_notes: string | null;

    user: {
        name: string;
        email: string;
    };

    project: {
        name: string;
    } | null;
};

type Maintenance = {
    public_id: string;
    type: string;
    status: string;
    started_date: string | null;
    completed_date: string | null;
    vendor: string | null;
    currency: string | null;
    cost: string | null;
    description: string;
    resolution: string | null;
};

type Asset = {
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
    purchase_date: string | null;
    warranty_expires_at: string | null;
    notes: string | null;

    supplier: {
        name: string;
    } | null;

    project: {
        name: string;
    } | null;

    assignments: Assignment[];

    maintenances: Maintenance[];
};

type Props = {
    asset: Asset;
    users: UserOption[];
    projects: ProjectOption[];

    permissions: {
        assign: boolean;
        return: boolean;
        maintain: boolean;
        retire: boolean;
    };
};

export default function AssetShow({
    asset,
    users,
    projects,
    permissions,
}: Props) {
    const activeAssignment = asset.assignments.find(
        (assignment) => assignment.status === 'active',
    );

    const assignmentForm = useForm({
        user_id: '',
        project_id: '',
        assigned_date: new Date().toISOString().slice(0, 10),

        expected_return_date: '',

        location: asset.location ?? '',

        assignment_notes: '',
    });

    const returnForm = useForm({
        returned_date: new Date().toISOString().slice(0, 10),

        location: asset.location ?? '',

        return_notes: '',
    });

    const maintenanceForm = useForm({
        type: 'service',
        started_date: '',
        vendor: '',
        currency: asset.currency ?? 'LKR',
        cost: '',
        description: '',
    });

    const retirementForm = useForm({
        reason: '',
    });

    const assign = (event: FormEvent) => {
        event.preventDefault();

        assignmentForm.post(
            AssetAssignmentController.store(asset.public_id).url,
            {
                preserveScroll: true,
            },
        );
    };

    const returnAsset = (event: FormEvent) => {
        event.preventDefault();

        if (!activeAssignment) {
            return;
        }

        returnForm.post(
            AssetAssignmentController.returnAsset(activeAssignment.public_id)
                .url,
            {
                preserveScroll: true,
            },
        );
    };

    const maintain = (event: FormEvent) => {
        event.preventDefault();

        maintenanceForm.post(
            AssetMaintenanceController.store(asset.public_id).url,
            {
                preserveScroll: true,
            },
        );
    };

    const retire = (event: FormEvent) => {
        event.preventDefault();

        if (!window.confirm('Retire this asset?')) {
            return;
        }

        router.post(
            AssetController.retire(asset.public_id).url,
            retirementForm.data,
            {
                preserveScroll: true,
            },
        );
    };

    return (
        <>
            <Head title={asset.asset_number} />

            <div className="space-y-6 p-6">
                <header>
                    <Link
                        href={AssetController.index().url}
                        className="text-sm underline"
                    >
                        ← Assets
                    </Link>

                    <div className="mt-3 flex items-center justify-between gap-3">
                        <div>
                            <h1 className="text-2xl font-semibold">
                                {asset.asset_number}
                            </h1>

                            <p className="text-muted-foreground">
                                {asset.name} · {asset.asset_tag}
                            </p>
                        </div>

                        <StatusBadge value={asset.status} />
                    </div>
                </header>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-3">
                    <Info label="Category" value={asset.category} />

                    <Info label="Brand" value={asset.brand ?? '-'} />

                    <Info label="Model" value={asset.model ?? '-'} />

                    <Info
                        label="Serial Number"
                        value={asset.serial_number ?? '-'}
                    />

                    <Info label="Location" value={asset.location ?? '-'} />

                    <Info label="Project" value={asset.project?.name ?? '-'} />

                    <Info
                        label="Supplier"
                        value={asset.supplier?.name ?? '-'}
                    />

                    <Info
                        label="Purchase Date"
                        value={asset.purchase_date ?? '-'}
                    />

                    <Info
                        label="Warranty Expiry"
                        value={asset.warranty_expires_at ?? '-'}
                    />

                    {asset.currency && asset.purchase_cost && (
                        <Info
                            label="Purchase Cost"
                            value={`${asset.currency} ${asset.purchase_cost}`}
                        />
                    )}
                </section>

                {permissions.assign && asset.status === 'available' && (
                    <form
                        onSubmit={assign}
                        className="space-y-4 rounded border p-5"
                    >
                        <h2 className="font-semibold">Assign Asset</h2>

                        <div className="grid gap-4 md:grid-cols-2">
                            <select
                                value={assignmentForm.data.user_id}
                                onChange={(event) =>
                                    assignmentForm.setData(
                                        'user_id',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                            >
                                <option value="">Select employee</option>

                                {users.map((user) => (
                                    <option key={user.id} value={user.id}>
                                        {user.name} ({user.email})
                                    </option>
                                ))}
                            </select>

                            <select
                                value={assignmentForm.data.project_id}
                                onChange={(event) =>
                                    assignmentForm.setData(
                                        'project_id',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                            >
                                <option value="">No project</option>

                                {projects.map((project) => (
                                    <option key={project.id} value={project.id}>
                                        {project.name}
                                    </option>
                                ))}
                            </select>

                            <input
                                type="date"
                                value={assignmentForm.data.assigned_date}
                                onChange={(event) =>
                                    assignmentForm.setData(
                                        'assigned_date',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                            />

                            <input
                                type="date"
                                value={assignmentForm.data.expected_return_date}
                                onChange={(event) =>
                                    assignmentForm.setData(
                                        'expected_return_date',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                            />

                            <input
                                value={assignmentForm.data.location}
                                onChange={(event) =>
                                    assignmentForm.setData(
                                        'location',
                                        event.target.value,
                                    )
                                }
                                placeholder="Location"
                                className="rounded border p-2 md:col-span-2"
                            />
                        </div>

                        <textarea
                            value={assignmentForm.data.assignment_notes}
                            onChange={(event) =>
                                assignmentForm.setData(
                                    'assignment_notes',
                                    event.target.value,
                                )
                            }
                            placeholder="Assignment notes"
                            className="min-h-24 w-full rounded border p-2"
                        />

                        <button
                            disabled={assignmentForm.processing}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            Assign Asset
                        </button>
                    </form>
                )}

                {permissions.return && activeAssignment && (
                    <form
                        onSubmit={returnAsset}
                        className="space-y-4 rounded border p-5"
                    >
                        <h2 className="font-semibold">Return Asset</h2>

                        <p className="text-sm">
                            Currently assigned to{' '}
                            <strong>{activeAssignment.user.name}</strong>
                        </p>

                        <div className="grid gap-4 md:grid-cols-2">
                            <input
                                type="date"
                                value={returnForm.data.returned_date}
                                onChange={(event) =>
                                    returnForm.setData(
                                        'returned_date',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                            />

                            <input
                                value={returnForm.data.location}
                                onChange={(event) =>
                                    returnForm.setData(
                                        'location',
                                        event.target.value,
                                    )
                                }
                                placeholder="Return location"
                                className="rounded border p-2"
                            />
                        </div>

                        <textarea
                            value={returnForm.data.return_notes}
                            onChange={(event) =>
                                returnForm.setData(
                                    'return_notes',
                                    event.target.value,
                                )
                            }
                            placeholder="Return notes"
                            className="min-h-24 w-full rounded border p-2"
                        />

                        <button
                            disabled={returnForm.processing}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            Return Asset
                        </button>
                    </form>
                )}

                {permissions.maintain &&
                    !['retired', 'disposed'].includes(asset.status) && (
                        <form
                            onSubmit={maintain}
                            className="space-y-4 rounded border p-5"
                        >
                            <h2 className="font-semibold">Add Maintenance</h2>

                            <div className="grid gap-4 md:grid-cols-3">
                                <select
                                    value={maintenanceForm.data.type}
                                    onChange={(event) =>
                                        maintenanceForm.setData(
                                            'type',
                                            event.target.value,
                                        )
                                    }
                                    className="rounded border p-2"
                                >
                                    <option value="inspection">
                                        Inspection
                                    </option>
                                    <option value="repair">Repair</option>
                                    <option value="service">Service</option>
                                    <option value="upgrade">Upgrade</option>
                                </select>

                                <input
                                    type="date"
                                    value={maintenanceForm.data.started_date}
                                    onChange={(event) =>
                                        maintenanceForm.setData(
                                            'started_date',
                                            event.target.value,
                                        )
                                    }
                                    className="rounded border p-2"
                                />

                                <input
                                    value={maintenanceForm.data.vendor}
                                    onChange={(event) =>
                                        maintenanceForm.setData(
                                            'vendor',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Vendor"
                                    className="rounded border p-2"
                                />

                                <input
                                    value={maintenanceForm.data.currency}
                                    onChange={(event) =>
                                        maintenanceForm.setData(
                                            'currency',
                                            event.target.value.toUpperCase(),
                                        )
                                    }
                                    placeholder="Currency"
                                    className="rounded border p-2"
                                />

                                <input
                                    value={maintenanceForm.data.cost}
                                    onChange={(event) =>
                                        maintenanceForm.setData(
                                            'cost',
                                            event.target.value,
                                        )
                                    }
                                    placeholder="Cost"
                                    className="rounded border p-2"
                                />
                            </div>

                            <textarea
                                value={maintenanceForm.data.description}
                                onChange={(event) =>
                                    maintenanceForm.setData(
                                        'description',
                                        event.target.value,
                                    )
                                }
                                placeholder="Maintenance description"
                                className="min-h-24 w-full rounded border p-2"
                            />

                            <button
                                disabled={maintenanceForm.processing}
                                className="rounded bg-primary px-4 py-2 text-primary-foreground"
                            >
                                Add Maintenance
                            </button>
                        </form>
                    )}

                <section className="rounded border p-5">
                    <h2 className="mb-4 font-semibold">Assignment History</h2>

                    <div className="space-y-2">
                        {asset.assignments.map((assignment) => (
                            <div
                                key={assignment.public_id}
                                className="grid gap-2 rounded border p-3 text-sm md:grid-cols-4"
                            >
                                <span>{assignment.user.name}</span>

                                <span>{assignment.assigned_date}</span>

                                <span>{assignment.returned_date ?? '-'}</span>

                                <StatusBadge value={assignment.status} />
                            </div>
                        ))}

                        {asset.assignments.length === 0 && (
                            <p className="text-sm text-muted-foreground">
                                No assignment history.
                            </p>
                        )}
                    </div>
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 font-semibold">Maintenance History</h2>

                    <div className="space-y-2">
                        {asset.maintenances.map((maintenance) => (
                            <div
                                key={maintenance.public_id}
                                className="rounded border p-3 text-sm"
                            >
                                <div className="flex justify-between gap-3">
                                    <strong className="capitalize">
                                        {maintenance.type}
                                    </strong>

                                    <StatusBadge value={maintenance.status} />
                                </div>

                                <p className="mt-2">
                                    {maintenance.description}
                                </p>

                                {maintenance.cost && maintenance.currency && (
                                    <p className="mt-2 text-muted-foreground">
                                        Cost: {maintenance.currency}{' '}
                                        {maintenance.cost}
                                    </p>
                                )}
                            </div>
                        ))}

                        {asset.maintenances.length === 0 && (
                            <p className="text-sm text-muted-foreground">
                                No maintenance history.
                            </p>
                        )}
                    </div>
                </section>

                {permissions.retire &&
                    !['retired', 'disposed'].includes(asset.status) && (
                        <form
                            onSubmit={retire}
                            className="space-y-4 rounded border p-5"
                        >
                            <h2 className="font-semibold">Retire Asset</h2>

                            <textarea
                                value={retirementForm.data.reason}
                                onChange={(event) =>
                                    retirementForm.setData(
                                        'reason',
                                        event.target.value,
                                    )
                                }
                                placeholder="Retirement reason"
                                className="min-h-24 w-full rounded border p-2"
                            />

                            <button
                                type="submit"
                                disabled={
                                    retirementForm.processing ||
                                    Boolean(activeAssignment)
                                }
                                className="rounded border px-4 py-2 disabled:opacity-50"
                            >
                                Retire Asset
                            </button>

                            {activeAssignment && (
                                <p className="text-sm text-muted-foreground">
                                    Return the active assignment before retiring
                                    this asset.
                                </p>
                            )}
                        </form>
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

function StatusBadge({ value }: { value: string }) {
    return (
        <span className="inline-block rounded-full border px-2 py-1 text-xs capitalize">
            {value.replaceAll('_', ' ')}
        </span>
    );
}
