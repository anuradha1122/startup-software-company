import { Head, Link, router } from '@inertiajs/react';

export default function ProjectIndex({ projects, filters }: any) {
    return (
        <>
            <Head title="Projects" />

            <div className="p-6">
                <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">Projects</h1>

                        <p className="text-sm text-muted-foreground">
                            Project setup and governance.
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href="/admin/projects/export/excel"
                            className="rounded border px-4 py-2"
                        >
                            Excel
                        </a>

                        <Link
                            href="/admin/projects/create"
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            New Project
                        </Link>
                    </div>
                </div>

                <form
                    className="mb-5 flex gap-3"
                    onSubmit={(event) => {
                        event.preventDefault();

                        const data = new FormData(event.currentTarget);

                        router.get(
                            '/admin/projects',
                            Object.fromEntries(data.entries()),
                            {
                                preserveState: true,
                            },
                        );
                    }}
                >
                    <input
                        name="search"
                        defaultValue={filters.search ?? ''}
                        placeholder="Search projects"
                        className="rounded border p-2"
                    />

                    <select
                        name="status"
                        defaultValue={filters.status ?? ''}
                        className="rounded border p-2"
                    >
                        <option value="">All statuses</option>

                        {[
                            'planned',
                            'active',
                            'on_hold',
                            'completed',
                            'cancelled',
                        ].map((status) => (
                            <option key={status} value={status}>
                                {status}
                            </option>
                        ))}
                    </select>

                    <button className="rounded border px-4 py-2">Filter</button>
                </form>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Project</th>

                                <th className="p-3 text-left">Client</th>

                                <th className="p-3 text-left">Manager</th>

                                <th className="p-3 text-left">Health</th>

                                <th className="p-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            {projects.data.map((project: any) => (
                                <tr
                                    key={project.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/projects/${project.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {project.project_number}
                                        </Link>

                                        <p>{project.name}</p>
                                    </td>

                                    <td className="p-3">
                                        {project.client?.name}
                                    </td>

                                    <td className="p-3">
                                        {project.project_manager?.name}
                                    </td>

                                    <td className="p-3">{project.health}</td>

                                    <td className="p-3">{project.status}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
