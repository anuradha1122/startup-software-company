import { Head, Link } from '@inertiajs/react';

export default function InternalCostIndex({ project, entries, summary }: any) {
    return (
        <>
            <Head title={`${project.project_number} Internal Costs`} />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Internal Project Cost
                        </h1>

                        <p>
                            {project.project_number}
                            {' — '}
                            {project.name}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/projects/${project.public_id}/internal-costs/export/excel`}
                            className="rounded border px-3 py-2"
                        >
                            Excel
                        </a>

                        <a
                            href={`/admin/projects/${project.public_id}/internal-costs/export/pdf`}
                            className="rounded border px-3 py-2"
                        >
                            PDF
                        </a>

                        <Link
                            href={`/admin/projects/${project.public_id}`}
                            className="rounded border px-3 py-2"
                        >
                            Project
                        </Link>
                    </div>
                </div>

                <section className="grid gap-4 md:grid-cols-2">
                    <div className="rounded border p-4">
                        <span className="text-sm text-muted-foreground">
                            Approved Hours
                        </span>

                        <p className="text-2xl font-semibold">
                            {Number(summary.hours).toFixed(2)}
                        </p>
                    </div>

                    <div className="rounded border p-4">
                        <span className="text-sm text-muted-foreground">
                            Internal Cost
                        </span>

                        <p className="text-2xl font-semibold">
                            {Number(summary.internal_cost).toFixed(2)}
                        </p>
                    </div>
                </section>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Date</th>

                                <th className="p-3 text-left">Person</th>

                                <th className="p-3 text-left">Task</th>

                                <th className="p-3 text-left">Hours</th>

                                <th className="p-3 text-left">Rate</th>

                                <th className="p-3 text-left">Cost</th>
                            </tr>
                        </thead>

                        <tbody>
                            {entries.map((entry: any) => (
                                <tr key={entry.public_id} className="border-t">
                                    <td className="p-3">{entry.work_date}</td>

                                    <td className="p-3">
                                        {entry.timesheet.user.name}
                                    </td>

                                    <td className="p-3">
                                        {entry.task.task_number}
                                    </td>

                                    <td className="p-3">{entry.hours}</td>

                                    <td className="p-3">
                                        {entry.cost_currency}{' '}
                                        {entry.hourly_cost_rate_snapshot}
                                    </td>

                                    <td className="p-3">
                                        {entry.cost_currency}{' '}
                                        {entry.internal_cost_amount}
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
