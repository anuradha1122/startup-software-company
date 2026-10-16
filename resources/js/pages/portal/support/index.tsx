import { Head, Link, useForm } from '@inertiajs/react';

type Project = {
    id: number;
    public_id: string;
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
};

type Props = {
    tickets: Ticket[];
    projects: Project[];
};

type FormData = {
    project_id: string;
    subject: string;
    description: string;
    priority: 'low' | 'medium' | 'high' | 'urgent';
    severity: 'minor' | 'normal' | 'major' | 'critical';
};

export default function PortalSupportIndex({ tickets, projects }: Props) {
    const form = useForm<FormData>({
        project_id: '',
        subject: '',
        description: '',
        priority: 'medium',
        severity: 'normal',
    });

    return (
        <>
            <Head title="Support" />

            <main className="mx-auto max-w-6xl space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">Support</h1>

                    <p className="text-sm text-muted-foreground">
                        Open and follow your support requests.
                    </p>
                </div>

                <form
                    className="grid gap-3 rounded border p-5 md:grid-cols-2"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/portal/support');
                    }}
                >
                    <select
                        value={form.data.project_id}
                        onChange={(event) =>
                            form.setData('project_id', event.target.value)
                        }
                        className="rounded border p-2"
                    >
                        <option value="">General support</option>

                        {projects.map((project) => (
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
                        className="rounded border p-2"
                    />

                    <textarea
                        value={form.data.description}
                        onChange={(event) =>
                            form.setData('description', event.target.value)
                        }
                        placeholder="Describe the issue"
                        className="min-h-32 rounded border p-2 md:col-span-2"
                    />

                    <select
                        value={form.data.priority}
                        onChange={(event) =>
                            form.setData(
                                'priority',
                                event.target.value as FormData['priority'],
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
                                event.target.value as FormData['severity'],
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
                        Submit Ticket
                    </button>
                </form>

                <section className="space-y-3">
                    {tickets.map((ticket) => (
                        <Link
                            key={ticket.public_id}
                            href={`/portal/support/${ticket.public_id}`}
                            className="block rounded border p-4"
                        >
                            <p className="font-medium">
                                {ticket.ticket_number}
                                {' — '}
                                {ticket.subject}
                            </p>

                            <p className="text-sm text-muted-foreground">
                                {ticket.status}
                                {' · '}
                                {ticket.priority}
                            </p>
                        </Link>
                    ))}
                </section>
            </main>
        </>
    );
}
