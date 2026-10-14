import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function LeadsIndex({
    stages,
    leads,
    sources,
    owners,
    filters,
}: any) {
    const [draggedLead, setDraggedLead] = useState<string | null>(null);

    const moveLead = (stageId: number) => {
        if (!draggedLead) {
            return;
        }

        router.patch(
            `/admin/leads/${draggedLead}/stage`,
            {
                stage_id: stageId,
            },
            {
                preserveScroll: true,
            },
        );

        setDraggedLead(null);
    };

    return (
        <>
            <Head title="CRM Leads" />

            <div className="p-6">
                <div className="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">CRM & Leads</h1>

                        <p className="text-sm text-muted-foreground">
                            Manage the sales pipeline and follow-ups.
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        <a
                            href="/admin/leads/export/excel"
                            className="rounded border px-3 py-2 text-sm"
                        >
                            Excel
                        </a>

                        <a
                            href="/admin/leads/export/pipeline-pdf"
                            className="rounded border px-3 py-2 text-sm"
                        >
                            Pipeline PDF
                        </a>

                        <Link
                            href="/admin/leads/create"
                            className="rounded bg-primary px-4 py-2 text-sm text-primary-foreground"
                        >
                            New Lead
                        </Link>
                    </div>
                </div>

                <form
                    className="mb-6 grid gap-3 md:grid-cols-4"
                    onSubmit={(event) => {
                        event.preventDefault();

                        const form = new FormData(event.currentTarget);

                        router.get(
                            '/admin/leads',
                            Object.fromEntries(form.entries()),
                            {
                                preserveState: true,
                            },
                        );
                    }}
                >
                    <input
                        name="search"
                        placeholder="Search..."
                        defaultValue={filters.search ?? ''}
                        className="rounded border p-2"
                    />

                    <select
                        name="source_id"
                        defaultValue={filters.source_id ?? ''}
                        className="rounded border bg-white p-2 dark:bg-gray-950"
                    >
                        <option value="">All sources</option>

                        {sources.map((source: any) => (
                            <option key={source.id} value={source.id}>
                                {source.name}
                            </option>
                        ))}
                    </select>

                    <select
                        name="owner_id"
                        defaultValue={filters.owner_id ?? ''}
                        className="rounded border bg-white p-2 dark:bg-gray-950"
                    >
                        <option value="">All owners</option>

                        {owners.map((owner: any) => (
                            <option key={owner.id} value={owner.id}>
                                {owner.name}
                            </option>
                        ))}
                    </select>

                    <button className="rounded border px-4 py-2">Filter</button>
                </form>

                <div className="flex gap-4 overflow-x-auto pb-4">
                    {stages.map((stage: any) => {
                        const stageLeads = leads.filter(
                            (lead: any) => lead.stage_id === stage.id,
                        );

                        return (
                            <div
                                key={stage.id}
                                className="max-w-75 min-w-75 rounded-lg border bg-muted/20 p-3"
                                onDragOver={(event) => event.preventDefault()}
                                onDrop={() => moveLead(stage.id)}
                            >
                                <div className="mb-3 flex items-center justify-between">
                                    <h2 className="font-semibold">
                                        {stage.name}
                                    </h2>

                                    <span className="rounded-full border px-2 py-0.5 text-xs">
                                        {stageLeads.length}
                                    </span>
                                </div>

                                <div className="space-y-3">
                                    {stageLeads.map((lead: any) => (
                                        <div
                                            key={lead.public_id}
                                            draggable
                                            onDragStart={() =>
                                                setDraggedLead(lead.public_id)
                                            }
                                            className="cursor-move rounded-lg border bg-background p-4 shadow-sm"
                                        >
                                            <Link
                                                href={`/admin/leads/${lead.public_id}`}
                                                className="font-medium hover:underline"
                                            >
                                                {lead.lead_number}
                                            </Link>

                                            <p className="mt-1">{lead.title}</p>

                                            {lead.company_name && (
                                                <p className="text-sm text-muted-foreground">
                                                    {lead.company_name}
                                                </p>
                                            )}

                                            <div className="mt-3 space-y-1 text-xs text-muted-foreground">
                                                <p>
                                                    Owner:{' '}
                                                    {lead.owner?.name ??
                                                        'Unassigned'}
                                                </p>

                                                <p>
                                                    Value: {lead.currency}{' '}
                                                    {lead.estimated_value ??
                                                        '0.00'}
                                                </p>

                                                <p>
                                                    Probability:{' '}
                                                    {lead.probability}%
                                                </p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>
        </>
    );
}
