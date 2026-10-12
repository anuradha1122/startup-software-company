import { Head, Link, router, useForm } from '@inertiajs/react';

export default function RequirementShow({ requirement }: any) {
    const version = requirement.versions?.[0];

    const approvalForm = useForm({
        client_contact_id: '',
    });

    const decisionForm = useForm({
        decision: 'approved',
        decision_notes: '',
    });

    const attachmentForm = useForm({
        title: '',
        visibility: 'internal',
        file: null as File | null,
    });

    const pendingApproval = version?.approvals?.find(
        (item: any) => item.status === 'pending',
    );

    return (
        <>
            <Head title={requirement.requirement_number} />

            <div className="max-w-7xl p-6">
                <div className="mb-6 flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {requirement.requirement_number}
                        </h1>

                        <p className="text-lg">{requirement.title}</p>

                        <p className="text-sm text-muted-foreground">
                            {requirement.client?.name}
                            {' · '}
                            {requirement.status}
                        </p>
                    </div>

                    {version && (
                        <div className="flex flex-wrap gap-2">
                            <a
                                href={`/admin/requirements/${requirement.public_id}/versions/${version.public_id}/srs-pdf`}
                                className="rounded border px-3 py-2"
                            >
                                SRS PDF
                            </a>

                            {version.status === 'draft' && (
                                <Link
                                    href={`/admin/requirements/${requirement.public_id}/versions/${version.public_id}/edit`}
                                    className="rounded border px-3 py-2"
                                >
                                    Edit Draft
                                </Link>
                            )}

                            <Link
                                href={`/admin/requirements/${requirement.public_id}/versions/${version.public_id}/estimates/create`}
                                className="rounded border px-3 py-2"
                            >
                                New Estimate
                            </Link>
                        </div>
                    )}
                </div>

                {version && (
                    <>
                        <section className="rounded border p-5">
                            <div className="mb-4 flex justify-between">
                                <h2 className="text-lg font-semibold">
                                    Version {version.version_number}
                                </h2>

                                <span>{version.status}</span>
                            </div>

                            <h3 className="font-medium">Objective</h3>

                            <p className="whitespace-pre-wrap">
                                {version.objective}
                            </p>

                            <div className="mt-5 grid gap-5 md:grid-cols-2">
                                <div>
                                    <h3 className="font-medium">
                                        Scope Included
                                    </h3>

                                    <p className="whitespace-pre-wrap">
                                        {version.scope_in}
                                    </p>
                                </div>

                                <div>
                                    <h3 className="font-medium">
                                        Scope Excluded
                                    </h3>

                                    <p className="whitespace-pre-wrap">
                                        {version.scope_out}
                                    </p>
                                </div>
                            </div>

                            <div className="mt-5 grid gap-5 lg:grid-cols-2">
                                <div>
                                    <h3 className="font-medium">
                                        Functional Requirements
                                    </h3>

                                    <ol className="list-decimal pl-5">
                                        {(
                                            version.functional_requirements ??
                                            []
                                        ).map((item: string, index: number) => (
                                            <li key={index}>{item}</li>
                                        ))}
                                    </ol>
                                </div>

                                <div>
                                    <h3 className="font-medium">
                                        Non-Functional Requirements
                                    </h3>

                                    <ol className="list-decimal pl-5">
                                        {(
                                            version.non_functional_requirements ??
                                            []
                                        ).map((item: string, index: number) => (
                                            <li key={index}>{item}</li>
                                        ))}
                                    </ol>
                                </div>
                            </div>
                        </section>

                        <section className="mt-6 rounded border p-5">
                            <h2 className="mb-4 text-lg font-semibold">
                                Workflow
                            </h2>

                            <div className="flex flex-wrap gap-2">
                                {version.status === 'draft' && (
                                    <button
                                        onClick={() =>
                                            router.post(
                                                `/admin/requirements/${requirement.public_id}/versions/${version.public_id}/review`,
                                            )
                                        }
                                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                                    >
                                        Submit for Review
                                    </button>
                                )}

                                {[
                                    'approved',
                                    'revision_required',
                                    'in_review',
                                    'pending_client_approval',
                                ].includes(version.status) && (
                                    <button
                                        onClick={() =>
                                            router.post(
                                                `/admin/requirements/${requirement.public_id}/revisions`,
                                            )
                                        }
                                        className="rounded border px-4 py-2"
                                    >
                                        Create Revision
                                    </button>
                                )}
                            </div>

                            {version.status === 'in_review' && (
                                <form
                                    className="mt-5 flex max-w-xl gap-2"
                                    onSubmit={(event) => {
                                        event.preventDefault();

                                        approvalForm.post(
                                            `/admin/requirements/${requirement.public_id}/versions/${version.public_id}/approval-request`,
                                        );
                                    }}
                                >
                                    <select
                                        className="flex-1 rounded border p-2"
                                        value={
                                            approvalForm.data.client_contact_id
                                        }
                                        onChange={(e) =>
                                            approvalForm.setData(
                                                'client_contact_id',
                                                e.target.value,
                                            )
                                        }
                                    >
                                        <option value="">
                                            Select Client Contact
                                        </option>

                                        {requirement.client.contacts.map(
                                            (contact: any) => (
                                                <option
                                                    key={contact.id}
                                                    value={contact.id}
                                                >
                                                    {contact.name}
                                                    {' — '}
                                                    {contact.email}
                                                </option>
                                            ),
                                        )}
                                    </select>

                                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                                        Request Client Approval
                                    </button>
                                </form>
                            )}

                            {pendingApproval && (
                                <form
                                    className="mt-5 max-w-xl space-y-3"
                                    onSubmit={(event) => {
                                        event.preventDefault();

                                        decisionForm.post(
                                            `/admin/requirements/${requirement.public_id}/versions/${version.public_id}/approvals/${pendingApproval.public_id}/decision`,
                                        );
                                    }}
                                >
                                    <select
                                        className="w-full rounded border p-2"
                                        value={decisionForm.data.decision}
                                        onChange={(e) =>
                                            decisionForm.setData(
                                                'decision',
                                                e.target.value,
                                            )
                                        }
                                    >
                                        <option value="approved">
                                            Approved
                                        </option>

                                        <option value="revision_required">
                                            Revision Required
                                        </option>
                                    </select>

                                    <textarea
                                        placeholder="Decision notes"
                                        className="min-h-24 w-full rounded border p-2"
                                        value={decisionForm.data.decision_notes}
                                        onChange={(e) =>
                                            decisionForm.setData(
                                                'decision_notes',
                                                e.target.value,
                                            )
                                        }
                                    />

                                    <button className="rounded border px-4 py-2">
                                        Record Client Decision
                                    </button>
                                </form>
                            )}
                        </section>

                        <section className="mt-6 rounded border p-5">
                            <h2 className="mb-4 text-lg font-semibold">
                                Attachments
                            </h2>

                            <form
                                className="mb-5 grid gap-3 md:grid-cols-3"
                                onSubmit={(event) => {
                                    event.preventDefault();

                                    attachmentForm.post(
                                        `/admin/requirements/${requirement.public_id}/versions/${version.public_id}/attachments`,
                                        {
                                            forceFormData: true,
                                            preserveScroll: true,
                                        },
                                    );
                                }}
                            >
                                <input
                                    className="rounded border p-2"
                                    placeholder="Title"
                                    value={attachmentForm.data.title}
                                    onChange={(e) =>
                                        attachmentForm.setData(
                                            'title',
                                            e.target.value,
                                        )
                                    }
                                />

                                <select
                                    className="rounded border p-2"
                                    value={attachmentForm.data.visibility}
                                    onChange={(e) =>
                                        attachmentForm.setData(
                                            'visibility',
                                            e.target.value,
                                        )
                                    }
                                >
                                    <option value="internal">Internal</option>

                                    <option value="client">
                                        Client-visible
                                    </option>
                                </select>

                                <input
                                    type="file"
                                    onChange={(e) =>
                                        attachmentForm.setData(
                                            'file',
                                            e.target.files?.[0] ?? null,
                                        )
                                    }
                                />

                                <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                                    Upload
                                </button>
                            </form>

                            {version.attachments.map((attachment: any) => (
                                <div
                                    key={attachment.public_id}
                                    className="mb-2 flex justify-between rounded border p-3"
                                >
                                    <div>
                                        <p className="font-medium">
                                            {attachment.title}
                                        </p>

                                        <p className="text-sm text-muted-foreground">
                                            {attachment.visibility}
                                        </p>
                                    </div>

                                    <a
                                        href={`/admin/requirements/${requirement.public_id}/versions/${version.public_id}/attachments/${attachment.public_id}/download`}
                                        className="underline"
                                    >
                                        Download
                                    </a>
                                </div>
                            ))}
                        </section>

                        <section className="mt-6 rounded border p-5">
                            <h2 className="mb-4 text-lg font-semibold">
                                Estimates
                            </h2>

                            {version.estimates.map((estimate: any) => (
                                <div
                                    key={estimate.public_id}
                                    className="mb-3 flex justify-between rounded border p-4"
                                >
                                    <div>
                                        <Link
                                            href={`/admin/estimates/${estimate.public_id}/edit`}
                                            className="font-medium underline"
                                        >
                                            {estimate.estimate_number}
                                        </Link>

                                        <p className="text-sm">
                                            {estimate.currency}{' '}
                                            {estimate.estimated_revenue}
                                            {' · '}
                                            Margin{' '}
                                            {estimate.gross_margin_percent}%
                                        </p>
                                    </div>

                                    <span>{estimate.status}</span>
                                </div>
                            ))}
                        </section>
                    </>
                )}

                <section className="mt-6 rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Version History
                    </h2>

                    {requirement.versions.map((item: any) => (
                        <div
                            key={item.public_id}
                            className="mb-2 flex justify-between border-b pb-2"
                        >
                            <span>Version {item.version_number}</span>

                            <span>{item.status}</span>
                        </div>
                    ))}
                </section>
            </div>
        </>
    );
}
