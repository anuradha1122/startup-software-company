import { Head, Link, useForm } from '@inertiajs/react';

type Project = {
    id: number;
    public_id: string;
    project_number: string;
    name: string;
};

type ChangeRequest = {
    public_id: string;
    change_request_number: string;
    title: string;
    priority: string;
    status: string;
    submitted_at: string | null;
    project: {
        project_number: string;
        name: string;
    };
    estimate?: {
        currency: string;
        client_amount: string;
        estimated_days: number;
        client_notes?: string | null;
    } | null;
};

type Props = {
    changeRequests: ChangeRequest[];
    projects: Project[];
};

type FormData = {
    project_id: string;
    title: string;
    description: string;
    business_reason: string;
    priority: 'low' | 'medium' | 'high' | 'urgent';
};

export default function ChangeRequestsIndex({
    changeRequests,
    projects,
}: Props) {
    const form = useForm<FormData>({
        project_id: '',
        title: '',
        description: '',
        business_reason: '',
        priority: 'medium',
    });

    return (
        <>
            <Head title="Change Requests" />

            <main className="mx-auto max-w-6xl space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">Change Requests</h1>

                    <p className="text-sm text-muted-foreground">
                        Request changes to active or existing projects.
                    </p>
                </div>

                <form
                    className="grid gap-3 rounded border p-5 md:grid-cols-2"
                    onSubmit={(event) => {
                        event.preventDefault();

                        const project = form.data.project_id;

                        if (!project) {
                            return;
                        }

                        form.post(
                            `/portal/projects/${project}/change-requests`,
                        );
                    }}
                >
                    <select
                        value={form.data.project_id}
                        onChange={(event) =>
                            form.setData('project_id', event.target.value)
                        }
                        className="rounded border p-2"
                    >
                        <option value="">Select project</option>

                        {projects.map((project) => (
                            <option key={project.id} value={project.public_id}>
                                {project.project_number}
                                {' — '}
                                {project.name}
                            </option>
                        ))}
                    </select>

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

                    <input
                        value={form.data.title}
                        onChange={(event) =>
                            form.setData('title', event.target.value)
                        }
                        placeholder="Change title"
                        className="rounded border p-2 md:col-span-2"
                    />

                    <textarea
                        value={form.data.description}
                        onChange={(event) =>
                            form.setData('description', event.target.value)
                        }
                        placeholder="Describe the requested change"
                        className="min-h-32 rounded border p-2 md:col-span-2"
                    />

                    <textarea
                        value={form.data.business_reason}
                        onChange={(event) =>
                            form.setData('business_reason', event.target.value)
                        }
                        placeholder="Business reason"
                        className="min-h-24 rounded border p-2 md:col-span-2"
                    />

                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                        Submit Change Request
                    </button>
                </form>

                <section className="space-y-3">
                    {changeRequests.map((request) => (
                        <Link
                            key={request.public_id}
                            href={`/portal/change-requests/${request.public_id}`}
                            className="block rounded border p-4"
                        >
                            <p className="font-medium">
                                {request.change_request_number}
                                {' — '}
                                {request.title}
                            </p>

                            <p className="text-sm text-muted-foreground">
                                {request.project.project_number}
                                {' · '}
                                {request.status}
                            </p>

                            {request.estimate && (
                                <p className="mt-2 text-sm">
                                    Estimate: {request.estimate.currency}{' '}
                                    {request.estimate.client_amount}
                                </p>
                            )}
                        </Link>
                    ))}
                </section>
            </main>
        </>
    );
}
