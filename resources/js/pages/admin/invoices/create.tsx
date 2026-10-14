import { Head, useForm } from '@inertiajs/react';

type ClientContact = {
    id: number;
    public_id: string;
    name: string;
    email?: string | null;
    is_billing?: boolean;
};

type Client = {
    id: number;
    public_id: string;
    name: string;
    default_currency?: string | null;
    address?: string | null;
    contacts: ClientContact[];
};

type Project = {
    id: number;
    public_id: string;
    project_number: string;
    name: string;
    currency: string;
    client: Client;
};

type Props = {
    projects: Project[];
};

type InvoiceForm = {
    project_id: string;
    billing_contact_id: string;
    invoice_date: string;
    due_date: string;
    currency: string;
    billing_name: string;
    billing_email: string;
    billing_address: string;
    notes: string;
    payment_instructions: string;
};

export default function InvoiceCreate({ projects }: Props) {
    const form = useForm<InvoiceForm>({
        project_id: '',
        billing_contact_id: '',
        invoice_date: '',
        due_date: '',
        currency: 'LKR',
        billing_name: '',
        billing_email: '',
        billing_address: '',
        notes: '',
        payment_instructions: '',
    });

    const selectedProject = projects.find(
        (project) => String(project.id) === form.data.project_id,
    );

    const handleProjectChange = (projectId: string) => {
        form.setData('project_id', projectId);

        const project = projects.find((item) => String(item.id) === projectId);

        if (!project) {
            form.setData('billing_contact_id', '');

            return;
        }

        const billingContact =
            project.client.contacts.find((contact) => contact.is_billing) ??
            project.client.contacts[0];

        form.setData(
            'currency',
            project.currency || project.client.default_currency || 'LKR',
        );

        form.setData('billing_name', project.client.name);

        form.setData('billing_address', project.client.address ?? '');

        form.setData(
            'billing_contact_id',
            billingContact ? String(billingContact.id) : '',
        );

        form.setData('billing_email', billingContact?.email ?? '');
    };

    const handleContactChange = (contactId: string) => {
        form.setData('billing_contact_id', contactId);

        const contact = selectedProject?.client.contacts.find(
            (item) => String(item.id) === contactId,
        );

        form.setData('billing_email', contact?.email ?? '');
    };

    return (
        <>
            <Head title="New Invoice" />

            <form
                onSubmit={(event) => {
                    event.preventDefault();

                    form.post('/admin/invoices');
                }}
                className="mx-auto max-w-5xl space-y-6 p-6"
            >
                <div>
                    <h1 className="text-2xl font-semibold">New Invoice</h1>

                    <p className="text-sm text-muted-foreground">
                        Create the draft first, then add invoice lines.
                    </p>
                </div>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-2">
                    <label className="space-y-1">
                        <span className="text-sm font-medium">Project</span>

                        <select
                            value={form.data.project_id}
                            onChange={(event) =>
                                handleProjectChange(event.target.value)
                            }
                            className="w-full rounded border p-2"
                        >
                            <option value="">Select project</option>

                            {projects.map((project) => (
                                <option key={project.id} value={project.id}>
                                    {project.project_number}
                                    {' — '}
                                    {project.name}
                                </option>
                            ))}
                        </select>

                        {form.errors.project_id && (
                            <p className="text-sm text-destructive">
                                {form.errors.project_id}
                            </p>
                        )}
                    </label>

                    <label className="space-y-1">
                        <span className="text-sm font-medium">
                            Billing Contact
                        </span>

                        <select
                            value={form.data.billing_contact_id}
                            onChange={(event) =>
                                handleContactChange(event.target.value)
                            }
                            className="w-full rounded border p-2"
                        >
                            <option value="">No contact</option>

                            {selectedProject?.client.contacts.map((contact) => (
                                <option key={contact.id} value={contact.id}>
                                    {contact.name}
                                    {contact.email ? ` — ${contact.email}` : ''}
                                </option>
                            ))}
                        </select>
                    </label>

                    <label className="space-y-1">
                        <span className="text-sm font-medium">
                            Invoice Date
                        </span>

                        <input
                            type="date"
                            value={form.data.invoice_date}
                            onChange={(event) =>
                                form.setData('invoice_date', event.target.value)
                            }
                            className="w-full rounded border p-2"
                        />
                    </label>

                    <label className="space-y-1">
                        <span className="text-sm font-medium">Due Date</span>

                        <input
                            type="date"
                            value={form.data.due_date}
                            onChange={(event) =>
                                form.setData('due_date', event.target.value)
                            }
                            className="w-full rounded border p-2"
                        />
                    </label>

                    <label className="space-y-1">
                        <span className="text-sm font-medium">Currency</span>

                        <input
                            maxLength={3}
                            value={form.data.currency}
                            onChange={(event) =>
                                form.setData(
                                    'currency',
                                    event.target.value.toUpperCase(),
                                )
                            }
                            className="w-full rounded border p-2"
                        />
                    </label>

                    <label className="space-y-1">
                        <span className="text-sm font-medium">
                            Billing Name
                        </span>

                        <input
                            value={form.data.billing_name}
                            onChange={(event) =>
                                form.setData('billing_name', event.target.value)
                            }
                            className="w-full rounded border p-2"
                        />
                    </label>

                    <label className="space-y-1">
                        <span className="text-sm font-medium">
                            Billing Email
                        </span>

                        <input
                            type="email"
                            value={form.data.billing_email}
                            onChange={(event) =>
                                form.setData(
                                    'billing_email',
                                    event.target.value,
                                )
                            }
                            className="w-full rounded border p-2"
                        />
                    </label>

                    <label className="space-y-1 md:col-span-2">
                        <span className="text-sm font-medium">
                            Billing Address
                        </span>

                        <textarea
                            value={form.data.billing_address}
                            onChange={(event) =>
                                form.setData(
                                    'billing_address',
                                    event.target.value,
                                )
                            }
                            className="min-h-24 w-full rounded border p-2"
                        />
                    </label>

                    <label className="space-y-1 md:col-span-2">
                        <span className="text-sm font-medium">
                            Payment Instructions
                        </span>

                        <textarea
                            value={form.data.payment_instructions}
                            onChange={(event) =>
                                form.setData(
                                    'payment_instructions',
                                    event.target.value,
                                )
                            }
                            className="min-h-24 w-full rounded border p-2"
                        />
                    </label>

                    <label className="space-y-1 md:col-span-2">
                        <span className="text-sm font-medium">Notes</span>

                        <textarea
                            value={form.data.notes}
                            onChange={(event) =>
                                form.setData('notes', event.target.value)
                            }
                            className="min-h-24 w-full rounded border p-2"
                        />
                    </label>
                </section>

                <div className="flex justify-end">
                    <button
                        disabled={form.processing}
                        className="rounded bg-primary px-5 py-2 text-primary-foreground disabled:opacity-50"
                    >
                        Create Draft Invoice
                    </button>
                </div>
            </form>
        </>
    );
}
