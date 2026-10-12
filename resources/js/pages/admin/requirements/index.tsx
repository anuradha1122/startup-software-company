import { Head, Link, router } from '@inertiajs/react';

export default function RequirementsIndex({
    requirements,
    clients,
    filters,
}: any) {
    return (
        <>
            <Head title="Requirements" />

            <div className="p-6">
                <div className="mb-6 flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold">Requirements</h1>

                        <p className="text-sm text-muted-foreground">
                            Scope, versioning and client approvals.
                        </p>
                    </div>

                    <Link
                        href="/admin/requirements/create"
                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        New Requirement
                    </Link>
                </div>

                <form
                    className="mb-6 grid gap-3 md:grid-cols-4"
                    onSubmit={(event) => {
                        event.preventDefault();

                        const data = new FormData(event.currentTarget);

                        router.get(
                            '/admin/requirements',
                            Object.fromEntries(data.entries()),
                            {
                                preserveState: true,
                            },
                        );
                    }}
                >
                    <input
                        name="search"
                        placeholder="Search..."
                        defaultValue={filters.search ?? ''}
                        className="rounded border p-2"
                    />

                    <select
                        name="client_id"
                        defaultValue={filters.client_id ?? ''}
                        className="rounded border p-2"
                    >
                        <option value="">All clients</option>

                        {clients.map((client: any) => (
                            <option key={client.id} value={client.id}>
                                {client.name}
                            </option>
                        ))}
                    </select>

                    <select
                        name="status"
                        defaultValue={filters.status ?? ''}
                        className="rounded border p-2"
                    >
                        <option value="">All statuses</option>

                        <option value="draft">Draft</option>

                        <option value="in_review">In Review</option>

                        <option value="pending_client_approval">
                            Client Approval
                        </option>

                        <option value="approved">Approved</option>

                        <option value="revision_required">
                            Revision Required
                        </option>
                    </select>

                    <button className="rounded border px-4 py-2">Filter</button>
                </form>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Requirement</th>

                                <th className="p-3 text-left">Client</th>

                                <th className="p-3 text-left">Version</th>

                                <th className="p-3 text-left">Owner</th>

                                <th className="p-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            {requirements.data.map((item: any) => (
                                <tr key={item.public_id} className="border-t">
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/requirements/${item.public_id}`}
                                            className="font-medium hover:underline"
                                        >
                                            {item.requirement_number}
                                        </Link>

                                        <p>{item.title}</p>
                                    </td>

                                    <td className="p-3">{item.client?.name}</td>

                                    <td className="p-3">
                                        v
                                        {item.latest_version?.version_number ??
                                            '-'}
                                    </td>

                                    <td className="p-3">
                                        {item.owner?.name ?? 'Unassigned'}
                                    </td>

                                    <td className="p-3">{item.status}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
