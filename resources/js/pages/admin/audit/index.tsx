import { Head, router, useForm } from "@inertiajs/react";

export default function AuditIndex({ activities, filters }: any) {
    const form = useForm({
        search: filters.search ?? "",
        log_name: filters.log_name ?? "",
        date_from: filters.date_from ?? "",
        date_to: filters.date_to ?? "",
    });

    const query = new URLSearchParams(
        Object.entries(form.data)
            .filter(([, value]) => Boolean(value))
            .reduce(
                (result, [key, value]) => ({
                    ...result,
                    [key]: String(value),
                }),
                {},
            ),
    ).toString();

    return (
        <>
            <Head title="Audit Log" />

            <div className="p-6">
                <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <h1 className="text-2xl font-semibold">Audit Log</h1>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/audit/excel?${query}`}
                            className="rounded border px-3 py-2"
                        >
                            Excel
                        </a>

                        <a
                            href={`/admin/audit/pdf?${query}`}
                            className="rounded border px-3 py-2"
                        >
                            PDF
                        </a>
                    </div>
                </div>

                <form
                    className="mb-6 grid gap-3 md:grid-cols-4"
                    onSubmit={(event) => {
                        event.preventDefault();

                        router.get("/admin/audit", form.data, {
                            preserveState: true,
                        });
                    }}
                >
                    <input
                        className="rounded border p-2"
                        placeholder="Search"
                        value={form.data.search}
                        onChange={(e) => form.setData("search", e.target.value)}
                    />

                    <input
                        className="rounded border p-2"
                        placeholder="Log name"
                        value={form.data.log_name}
                        onChange={(e) =>
                            form.setData("log_name", e.target.value)
                        }
                    />

                    <input
                        type="date"
                        className="rounded border p-2"
                        value={form.data.date_from}
                        onChange={(e) =>
                            form.setData("date_from", e.target.value)
                        }
                    />

                    <input
                        type="date"
                        className="rounded border p-2"
                        value={form.data.date_to}
                        onChange={(e) =>
                            form.setData("date_to", e.target.value)
                        }
                    />

                    <button className="rounded bg-slate-900 px-4 py-2 text-white">
                        Filter
                    </button>
                </form>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Date</th>
                                <th className="p-3 text-left">Log</th>
                                <th className="p-3 text-left">Event</th>
                                <th className="p-3 text-left">Description</th>
                                <th className="p-3 text-left">User</th>
                            </tr>
                        </thead>

                        <tbody>
                            {activities.data.map((activity: any) => (
                                <tr key={activity.id} className="border-t">
                                    <td className="p-3 uppercase">
                                        {new Date(
                                            activity.created_at,
                                        ).toLocaleString("en-CA")}
                                    </td>

                                    <td className="p-3">{activity.log_name}</td>

                                    <td className="p-3">{activity.event}</td>

                                    <td className="p-3">
                                        {activity.description}
                                    </td>

                                    <td className="p-3">
                                        {activity.causer?.name ?? "System"}
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
