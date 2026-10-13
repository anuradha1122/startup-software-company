import { Head, Link, router, useForm } from '@inertiajs/react';

type AssignmentForm = {
    user_id: string;
    role: string;
    allocation_percent: number;
    status: 'planned' | 'active';
    start_date: string;
    end_date: string;
    billable: boolean;
    responsibilities: string;
    notes: string;
};

export default function ProjectTeam({ project, availableUsers }: any) {
    const form = useForm<AssignmentForm>({
        user_id: '',
        role: '',
        allocation_percent: 100,

        status: project.status === 'active' ? 'active' : 'planned',

        start_date: project.planned_start_date ?? '',

        end_date: project.planned_end_date ?? '',

        billable: true,
        responsibilities: '',
        notes: '',
    });

    return (
        <>
            <Head title={`${project.project_number} Team`} />

            <div className="max-w-7xl space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">Project Team</h1>

                        <p>
                            {project.project_number}
                            {' — '}
                            {project.name}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/projects/${project.public_id}/team/export/excel`}
                            className="rounded border px-3 py-2"
                        >
                            Excel
                        </a>

                        <a
                            href={`/admin/projects/${project.public_id}/team/export/pdf`}
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

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Add Team Member
                    </h2>

                    <form
                        className="grid gap-3 md:grid-cols-3"
                        onSubmit={(event) => {
                            event.preventDefault();

                            form.post(
                                `/admin/projects/${project.public_id}/team`,
                                {
                                    preserveScroll: true,

                                    onSuccess: () => form.reset(),
                                },
                            );
                        }}
                    >
                        <select
                            className="rounded border p-2"
                            value={form.data.user_id}
                            onChange={(e) =>
                                form.setData('user_id', e.target.value)
                            }
                        >
                            <option value="">Select person</option>

                            {availableUsers.map((user: any) => (
                                <option key={user.id} value={user.id}>
                                    {user.name}
                                </option>
                            ))}
                        </select>

                        <input
                            className="rounded border p-2"
                            placeholder="Project role"
                            value={form.data.role}
                            onChange={(e) =>
                                form.setData('role', e.target.value)
                            }
                        />

                        <input
                            type="number"
                            min="1"
                            max="100"
                            className="rounded border p-2"
                            placeholder="Allocation %"
                            value={form.data.allocation_percent}
                            onChange={(e) =>
                                form.setData(
                                    'allocation_percent',
                                    Number(e.target.value),
                                )
                            }
                        />

                        <select
                            className="rounded border p-2"
                            value={form.data.status}
                            onChange={(e) =>
                                form.setData(
                                    'status',
                                    e.target.value as AssignmentForm['status'],
                                )
                            }
                        >
                            <option value="planned">Planned</option>

                            {project.status === 'active' && (
                                <option value="active">Active</option>
                            )}
                        </select>

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={form.data.start_date}
                            onChange={(e) =>
                                form.setData('start_date', e.target.value)
                            }
                        />

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={form.data.end_date}
                            onChange={(e) =>
                                form.setData('end_date', e.target.value)
                            }
                        />

                        <textarea
                            className="min-h-24 rounded border p-2 md:col-span-3"
                            placeholder="Responsibilities"
                            value={form.data.responsibilities}
                            onChange={(e) =>
                                form.setData('responsibilities', e.target.value)
                            }
                        />

                        <label className="flex items-center gap-2">
                            <input
                                type="checkbox"
                                checked={form.data.billable}
                                onChange={(e) =>
                                    form.setData('billable', e.target.checked)
                                }
                            />
                            Billable assignment
                        </label>

                        <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                            Add Assignment
                        </button>
                    </form>
                </section>

                <section className="rounded border">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="bg-muted">
                                <tr>
                                    <th className="p-3 text-left">Person</th>

                                    <th className="p-3 text-left">Role</th>

                                    <th className="p-3 text-left">
                                        Allocation
                                    </th>

                                    <th className="p-3 text-left">Period</th>

                                    <th className="p-3 text-left">Status</th>

                                    <th className="p-3 text-left">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                {project.assignments.map((assignment: any) => (
                                    <tr
                                        key={assignment.public_id}
                                        className="border-t"
                                    >
                                        <td className="p-3">
                                            <Link
                                                href={`/admin/people/${assignment.user.public_id}`}
                                                className="font-medium underline"
                                            >
                                                {assignment.user.name}
                                            </Link>
                                        </td>

                                        <td className="p-3">
                                            {assignment.role}
                                        </td>

                                        <td className="p-3">
                                            {assignment.allocation_percent}%
                                        </td>

                                        <td className="p-3">
                                            {assignment.start_date ?? '-'}
                                            {' → '}
                                            {assignment.end_date ?? '-'}
                                        </td>

                                        <td className="p-3">
                                            {assignment.status}
                                        </td>

                                        <td className="p-3">
                                            {['planned', 'active'].includes(
                                                assignment.status,
                                            ) && (
                                                <button
                                                    onClick={() =>
                                                        router.post(
                                                            `/admin/projects/${project.public_id}/team/${assignment.public_id}/end`,
                                                        )
                                                    }
                                                    className="rounded border px-3 py-1"
                                                >
                                                    End
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </>
    );
}
