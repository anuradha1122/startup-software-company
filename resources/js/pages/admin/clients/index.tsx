import { Head, Link, router } from '@inertiajs/react';

export default function ClientsIndex({ clients, owners, filters }: any) {
    const query = new URLSearchParams(
        Object.entries(filters ?? {})
            .filter(
                ([, value]) =>
                    value !== null && value !== undefined && value !== '',
            )
            .map(([key, value]) => [key, String(value)]),
    ).toString();

    return (
        <>
            <Head title="Clients" />

            <div className="p-6">
                <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">Clients</h1>

                        <p className="text-sm text-muted-foreground">
                            Client profiles, billing and documents.
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/clients/export/excel?${query}`}
                            className="rounded border px-3 py-2"
                        >
                            Export Excel
                        </a>

                        <Link
                            href="/admin/clients/create"
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            New Client
                        </Link>
                    </div>
                </div>

                <form
                    className="mb-6 grid gap-3 md:grid-cols-4"
                    onSubmit={(event) => {
                        event.preventDefault();

                        const data = new FormData(event.currentTarget);

                        router.get(
                            '/admin/clients',
                            Object.fromEntries(data.entries()),
                            {
                                preserveState: true,
                            },
                        );
                    }}
                >
                    <input
                        name="search"
                        placeholder="Search clients..."
                        defaultValue={filters.search ?? ''}
                        className="rounded border p-2"
                    />

                    <select
                        name="status"
                        defaultValue={filters.status ?? ''}
                        className="rounded border p-2"
                    >
                        <option value="">All statuses</option>

                        <option value="active">Active</option>

                        <option value="inactive">Inactive</option>
                    </select>

                    <select
                        name="owner_id"
                        defaultValue={filters.owner_id ?? ''}
                        className="rounded border p-2"
                    >
                        <option value="">All owners</option>

                        {owners.map((owner: any) => (
                            <option key={owner.id} value={owner.id}>
                                {owner.name}
                            </option>
                        ))}
                    </select>

                    <button className="rounded border px-4 py-2">Filter</button>
                </form>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Client</th>

                                <th className="p-3 text-left">Contact</th>

                                <th className="p-3 text-left">Owner</th>

                                <th className="p-3 text-left">Terms</th>

                                <th className="p-3 text-left">Status</th>

                                <th className="p-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            {clients.data.map((client: any) => (
                                <tr key={client.public_id} className="border-t">
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/clients/${client.public_id}`}
                                            className="font-medium hover:underline"
                                        >
                                            {client.client_number}
                                        </Link>

                                        <p>{client.name}</p>
                                    </td>

                                    <td className="p-3">
                                        {client.email}

                                        <p>{client.phone}</p>
                                    </td>

                                    <td className="p-3">
                                        {client.owner?.name ?? 'Unassigned'}
                                    </td>

                                    <td className="p-3">
                                        {client.default_payment_terms_days} days
                                    </td>

                                    <td className="p-3">{client.status}</td>

                                    <td className="p-3 text-right">
                                        <Link
                                            href={`/admin/clients/${client.public_id}`}
                                            className="underline"
                                        >
                                            View
                                        </Link>
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
