import { Head, Link, router } from '@inertiajs/react';

export default function SupplierIndex({ suppliers, filters }: any) {
    return (
        <>
            <Head title="Suppliers" />

            <div className="space-y-6 p-6">
                <div className="flex justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">Suppliers</h1>

                        <p className="text-sm text-muted-foreground">
                            External suppliers and service providers.
                        </p>
                    </div>

                    <Link
                        href="/admin/suppliers/create"
                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        New Supplier
                    </Link>
                </div>

                <form
                    className="flex gap-2"
                    onSubmit={(event) => {
                        event.preventDefault();

                        const form = new FormData(event.currentTarget);

                        router.get(
                            '/admin/suppliers',
                            Object.fromEntries(form.entries()),
                            {
                                preserveState: true,
                            },
                        );
                    }}
                >
                    <input
                        name="search"
                        className="rounded border p-2"
                        placeholder="Search suppliers"
                        defaultValue={filters.search ?? ''}
                    />

                    <button className="rounded border px-4 py-2">Search</button>
                </form>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Number</th>

                                <th className="p-3 text-left">Supplier</th>

                                <th className="p-3 text-left">Contact</th>

                                <th className="p-3 text-left">Currency</th>

                                <th className="p-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            {suppliers.data.map((supplier: any) => (
                                <tr
                                    key={supplier.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        {supplier.supplier_number}
                                    </td>

                                    <td className="p-3">
                                        <Link
                                            href={`/admin/suppliers/${supplier.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {supplier.name}
                                        </Link>
                                    </td>

                                    <td className="p-3">
                                        {supplier.email ?? '-'}
                                    </td>

                                    <td className="p-3">
                                        {supplier.default_currency}
                                    </td>

                                    <td className="p-3">{supplier.status}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
