import { Head, router, useForm } from '@inertiajs/react';

type Message = {
    public_id: string;
    author_type: string;
    internal_only: boolean;
    message: string;
    created_at: string;
    user?: {
        name: string;
    } | null;
    portal_user?: {
        name: string;
    } | null;
};

type Ticket = {
    public_id: string;
    ticket_number: string;
    subject: string;
    description: string;
    priority: 'low' | 'medium' | 'high' | 'urgent';
    severity: 'minor' | 'normal' | 'major' | 'critical';
    status:
        | 'open'
        | 'assigned'
        | 'in_progress'
        | 'waiting_client'
        | 'resolved'
        | 'closed'
        | 'cancelled';
    assigned_to: number | null;
    first_response_due_at?: string | null;
    resolution_due_at?: string | null;
    client: {
        name: string;
    };
    project?: {
        project_number: string;
        name: string;
    } | null;
    messages: Message[];
};

type Assignee = {
    id: number;
    public_id: string;
    name: string;
};

type Props = {
    ticket: Ticket;
    assignees: Assignee[];
};

type ReplyForm = {
    message: string;
    internal_only: boolean;
};

export default function SupportShow({ ticket, assignees }: Props) {
    const reply = useForm<ReplyForm>({
        message: '',
        internal_only: false,
    });

    return (
        <>
            <Head title={ticket.ticket_number} />

            <div className="space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">
                        {ticket.ticket_number}
                    </h1>

                    <p>{ticket.subject}</p>
                </div>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-2">
                    <div>
                        <p className="text-sm text-muted-foreground">Client</p>

                        <p>{ticket.client.name}</p>
                    </div>

                    <div>
                        <p className="text-sm text-muted-foreground">Project</p>

                        <p>
                            {ticket.project
                                ? `${ticket.project.project_number} — ${ticket.project.name}`
                                : '-'}
                        </p>
                    </div>
                </section>

                <section className="rounded border p-5">
                    <p className="whitespace-pre-wrap">{ticket.description}</p>
                </section>

                <section className="grid gap-3 rounded border p-5 md:grid-cols-4">
                    <select
                        defaultValue={ticket.priority}
                        id="priority"
                        className="rounded border p-2"
                    >
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>

                    <select
                        defaultValue={ticket.severity}
                        id="severity"
                        className="rounded border p-2"
                    >
                        <option value="minor">Minor</option>
                        <option value="normal">Normal</option>
                        <option value="major">Major</option>
                        <option value="critical">Critical</option>
                    </select>

                    <select
                        defaultValue={ticket.assigned_to ?? ''}
                        id="assigned_to"
                        className="rounded border p-2"
                    >
                        <option value="">Unassigned</option>

                        {assignees.map((assignee) => (
                            <option key={assignee.id} value={assignee.id}>
                                {assignee.name}
                            </option>
                        ))}
                    </select>

                    <select
                        defaultValue={ticket.status}
                        id="status"
                        className="rounded border p-2"
                    >
                        <option value="open">Open</option>

                        <option value="assigned">Assigned</option>

                        <option value="in_progress">In Progress</option>

                        <option value="waiting_client">Waiting Client</option>

                        <option value="resolved">Resolved</option>

                        <option value="closed">Closed</option>

                        <option value="cancelled">Cancelled</option>
                    </select>

                    <button
                        type="button"
                        onClick={() => {
                            const priority = (
                                document.getElementById(
                                    'priority',
                                ) as HTMLSelectElement
                            ).value;

                            const severity = (
                                document.getElementById(
                                    'severity',
                                ) as HTMLSelectElement
                            ).value;

                            const assignedTo = (
                                document.getElementById(
                                    'assigned_to',
                                ) as HTMLSelectElement
                            ).value;

                            const status = (
                                document.getElementById(
                                    'status',
                                ) as HTMLSelectElement
                            ).value;

                            router.put(
                                `/admin/support/${ticket.public_id}`,
                                {
                                    priority,
                                    severity,
                                    assigned_to: assignedTo
                                        ? Number(assignedTo)
                                        : null,
                                    status,
                                },
                                {
                                    preserveScroll: true,
                                },
                            );
                        }}
                        className="rounded border px-4 py-2"
                    >
                        Save Ticket
                    </button>
                </section>

                <section className="space-y-3">
                    <h2 className="text-lg font-semibold">Conversation</h2>

                    {ticket.messages.map((message) => (
                        <div
                            key={message.public_id}
                            className="rounded border p-4"
                        >
                            <div className="flex justify-between gap-3">
                                <strong>
                                    {message.author_type === 'client'
                                        ? message.portal_user?.name
                                        : message.user?.name}
                                </strong>

                                <span className="text-sm text-muted-foreground">
                                    {message.created_at}
                                </span>
                            </div>

                            {message.internal_only && (
                                <p className="mt-1 text-xs font-medium">
                                    Internal note
                                </p>
                            )}

                            <p className="mt-2 whitespace-pre-wrap">
                                {message.message}
                            </p>
                        </div>
                    ))}
                </section>

                {!['closed', 'cancelled'].includes(ticket.status) && (
                    <form
                        className="space-y-3 rounded border p-5"
                        onSubmit={(event) => {
                            event.preventDefault();

                            reply.post(
                                `/admin/support/${ticket.public_id}/messages`,
                                {
                                    preserveScroll: true,

                                    onSuccess: () => reply.reset(),
                                },
                            );
                        }}
                    >
                        <textarea
                            value={reply.data.message}
                            onChange={(event) =>
                                reply.setData('message', event.target.value)
                            }
                            className="min-h-32 w-full rounded border p-2"
                            placeholder="Reply"
                        />

                        <label className="flex items-center gap-2">
                            <input
                                type="checkbox"
                                checked={reply.data.internal_only}
                                onChange={(event) =>
                                    reply.setData(
                                        'internal_only',
                                        event.target.checked,
                                    )
                                }
                            />
                            Internal note only
                        </label>

                        <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                            Add Reply
                        </button>
                    </form>
                )}
            </div>
        </>
    );
}
