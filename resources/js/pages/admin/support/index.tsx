import { Head, Link, router, useForm } from '@inertiajs/react';

type ClientOption = {
    id: number;
    name: string;
};

type ProjectOption = {
    id: number;
    client_id: number;
    project_number: string;
    name: string;
};

type Ticket = {
    public_id: string;
    ticket_number: string;
    subject: string;
    priority: string;
    severity: string;
    status: string;
    created_at: string;
    client: {
        name: string;
    };
    project?: {
        project_number: string;
        name: string;
    } | null;
    assignee?: {
        name: string;
    } | null;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Props = {
    tickets: {
        data: Ticket[];
        links: PaginationLink[];
    };
    filters: {
        status?: string | null;
        priority?: string | null;
    };
    clients: ClientOption[];
    projects: ProjectOption[];
};

type CreateForm = {
    client_id: string;
    project_id: string;
    subject: string;
    description: string;
    priority: 'low' | 'medium' | 'high' | 'urgent';
    severity: 'minor' | 'normal' | 'major' | 'critical';
};

export default function SupportIndex({
    tickets,
    filters,
    clients,
    projects,
}: Props) {
    const form = useForm<CreateForm>({
        client_id: '',
        project_id: '',
        subject: '',
        description: '',
        priority: 'medium',
        severity: 'normal',
    });

    const availableProjects = projects.filter(
        (project) => String(project.client_id) === form.data.client_id,
    );

    return (
        <>
            <Head title="Support" />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Support Tickets
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            Client support, assignments, responses and SLA
                            tracking.
                        </p>
                    </div>

                    <a
                        href="/admin/support/export/excel"
                        className="rounded border px-4 py-2"
                    >
                        Export Excel
                    </a>
                </div>

                <form
                    className="grid gap-3 rounded border p-5 md:grid-cols-2"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/admin/support');
                    }}
                >
                    <select
                        value={form.data.client_id}
                        onChange={(event) => {
                            form.setData('client_id', event.target.value);

                            form.setData('project_id', '');
                        }}
                        className="rounded border p-2"
                    >
                        <option value="">Select client</option>

                        {clients.map((client) => (
                            <option key={client.id} value={client.id}>
                                {client.name}
                            </option>
                        ))}
                    </select>

                    <select
                        value={form.data.project_id}
                        onChange={(event) =>
                            form.setData('project_id', event.target.value)
                        }
                        className="rounded border p-2"
                    >
                        <option value="">No project</option>

                        {availableProjects.map((project) => (
                            <option key={project.id} value={project.id}>
                                {project.project_number}
                                {' — '}
                                {project.name}
                            </option>
                        ))}
                    </select>

                    <input
                        value={form.data.subject}
                        onChange={(event) =>
                            form.setData('subject', event.target.value)
                        }
                        placeholder="Subject"
                        className="rounded border p-2 md:col-span-2"
                    />

                    <textarea
                        value={form.data.description}
                        onChange={(event) =>
                            form.setData('description', event.target.value)
                        }
                        placeholder="Description"
                        className="min-h-28 rounded border p-2 md:col-span-2"
                    />

                    <select
                        value={form.data.priority}
                        onChange={(event) =>
                            form.setData(
                                'priority',
                                event.target.value as CreateForm['priority'],
                            )
                        }
                        className="rounded border p-2"
                    >
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>

                    <select
                        value={form.data.severity}
                        onChange={(event) =>
                            form.setData(
                                'severity',
                                event.target.value as CreateForm['severity'],
                            )
                        }
                        className="rounded border p-2"
                    >
                        <option value="minor">Minor</option>
                        <option value="normal">Normal</option>
                        <option value="major">Major</option>
                        <option value="critical">Critical</option>
                    </select>

                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                        Create Ticket
                    </button>
                </form>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Ticket</th>
                                <th className="p-3 text-left">Client</th>
                                <th className="p-3 text-left">Subject</th>
                                <th className="p-3 text-left">Priority</th>
                                <th className="p-3 text-left">Severity</th>
                                <th className="p-3 text-left">Status</th>
                                <th className="p-3 text-left">Assignee</th>
                            </tr>
                        </thead>

                        <tbody>
                            {tickets.data.map((ticket) => (
                                <tr key={ticket.public_id} className="border-t">
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/support/${ticket.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {ticket.ticket_number}
                                        </Link>
                                    </td>

                                    <td className="p-3">
                                        {ticket.client.name}
                                    </td>

                                    <td className="p-3">{ticket.subject}</td>

                                    <td className="p-3">{ticket.priority}</td>

                                    <td className="p-3">{ticket.severity}</td>

                                    <td className="p-3">{ticket.status}</td>

                                    <td className="p-3">
                                        {ticket.assignee?.name ?? '-'}
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
