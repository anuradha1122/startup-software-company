import AppLayout from "@/layouts/app-layout";
import { Head, Link, router, useForm } from "@inertiajs/react";

export default function LeadShow({ lead }: any) {
    const activity = useForm({
        type: "note",
        subject: "",
        description: "",
        due_at: "",
        completed: true,
    });

    return (
        <>
            <Head title={lead.lead_number} />

            <div className="max-w-6xl p-6">
                <div className="mb-6 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {lead.lead_number}
                        </h1>

                        <p className="text-lg">{lead.title}</p>

                        <p className="text-muted-foreground">
                            {lead.company_name}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <Link
                            href={`/admin/leads/${lead.public_id}/edit`}
                            className="rounded border px-3 py-2"
                        >
                            Edit
                        </Link>

                        {lead.status !== "converted" && (
                            <button
                                onClick={() => {
                                    if (
                                        confirm(
                                            "Convert this lead to a client?",
                                        )
                                    ) {
                                        router.post(
                                            `/admin/leads/${lead.public_id}/convert`,
                                        );
                                    }
                                }}
                                className="rounded bg-primary px-3 py-2 text-primary-foreground"
                            >
                                Convert to Client
                            </button>
                        )}
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    <section className="rounded border p-5 lg:col-span-2">
                        <h2 className="mb-4 text-lg font-semibold">
                            Lead Information
                        </h2>

                        <dl className="grid gap-4 md:grid-cols-2">
                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Stage
                                </dt>
                                <dd>{lead.stage?.name}</dd>
                            </div>

                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Owner
                                </dt>
                                <dd>{lead.owner?.name ?? "Unassigned"}</dd>
                            </div>

                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Source
                                </dt>
                                <dd>{lead.source?.name ?? "-"}</dd>
                            </div>

                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Priority
                                </dt>
                                <dd>{lead.priority}</dd>
                            </div>

                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Estimated Value
                                </dt>
                                <dd>
                                    {lead.currency} {lead.estimated_value}
                                </dd>
                            </div>

                            <div>
                                <dt className="text-sm text-muted-foreground">
                                    Probability
                                </dt>
                                <dd>{lead.probability}%</dd>
                            </div>
                        </dl>

                        {lead.notes && (
                            <div className="mt-5">
                                <p className="text-sm text-muted-foreground">
                                    Notes
                                </p>
                                <p className="whitespace-pre-wrap">
                                    {lead.notes}
                                </p>
                            </div>
                        )}
                    </section>

                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">Contacts</h2>

                        <div className="space-y-4">
                            {lead.contacts.map((contact: any) => (
                                <div
                                    key={contact.id}
                                    className="border-b pb-3 last:border-0"
                                >
                                    <p className="font-medium">
                                        {contact.name}
                                    </p>

                                    <p className="text-sm">
                                        {contact.designation}
                                    </p>

                                    <p className="text-sm">{contact.email}</p>

                                    <p className="text-sm">{contact.phone}</p>
                                </div>
                            ))}
                        </div>
                    </section>
                </div>

                <section className="mt-6 rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">Add Activity</h2>

                    <form
                        className="grid gap-4 md:grid-cols-2"
                        onSubmit={(event) => {
                            event.preventDefault();

                            activity.post(
                                `/admin/leads/${lead.public_id}/activities`,
                                {
                                    preserveScroll: true,

                                    onSuccess: () => activity.reset(),
                                },
                            );
                        }}
                    >
                        <select
                            className="rounded border bg-white dark:bg-gray-950 p-2"
                            value={activity.data.type}
                            onChange={(e) =>
                                activity.setData("type", e.target.value)
                            }
                        >
                            <option value="call">Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Meeting</option>
                            <option value="note">Note</option>
                            <option value="follow_up">Follow-up</option>
                        </select>

                        <input
                            placeholder="Subject"
                            className="rounded border p-2"
                            value={activity.data.subject}
                            onChange={(e) =>
                                activity.setData("subject", e.target.value)
                            }
                        />

                        <input
                            type="datetime-local"
                            className="rounded border p-2"
                            value={activity.data.due_at}
                            onChange={(e) =>
                                activity.setData("due_at", e.target.value)
                            }
                        />

                        <textarea
                            placeholder="Description"
                            className="rounded border p-2 md:col-span-2"
                            value={activity.data.description}
                            onChange={(e) =>
                                activity.setData("description", e.target.value)
                            }
                        />

                        <button
                            disabled={activity.processing}
                            className="rounded bg-primary px-4 py-2 text-primary-foreground"
                        >
                            Add Activity
                        </button>
                    </form>
                </section>

                <section className="mt-6 rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Activity Timeline
                    </h2>

                    <div className="space-y-4">
                        {lead.activities.map((item: any) => (
                            <div
                                key={item.id}
                                className="border-b pb-4 last:border-0"
                            >
                                <div className="flex justify-between">
                                    <div>
                                        <span className="font-medium">
                                            {item.subject}
                                        </span>

                                        <span className="ml-2 text-xs uppercase text-muted-foreground">
                                            {item.type}
                                        </span>
                                    </div>

                                    <span className="text-xs text-muted-foreground">
                                        {item.created_at}
                                    </span>
                                </div>

                                <p className="mt-1 whitespace-pre-wrap text-sm">
                                    {item.description}
                                </p>

                                <p className="mt-1 text-xs text-muted-foreground">
                                    By {item.creator?.name ?? "System"}
                                </p>
                            </div>
                        ))}
                    </div>
                </section>
            </div>
        </>
    );
}
