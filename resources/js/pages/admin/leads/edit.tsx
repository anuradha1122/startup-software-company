import LeadForm from '@/components/m01/lead-form';
import { Head, useForm } from '@inertiajs/react';

function localDateTime(value?: string | null) {
    if (!value) {
        return '';
    }

    return value.slice(0, 16);
}

export default function EditLead({ lead, stages, sources, owners }: any) {
    const form = useForm({
        title: lead.title ?? '',
        company_name: lead.company_name ?? '',
        stage_id: lead.stage_id?.toString() ?? '',
        source_id: lead.source_id?.toString() ?? '',
        owner_id: lead.owner_id?.toString() ?? '',
        estimated_value: lead.estimated_value ?? '',
        currency: lead.currency ?? 'LKR',
        priority: lead.priority ?? 'medium',

        next_follow_up_at: localDateTime(lead.next_follow_up_at),

        notes: lead.notes ?? '',
    });

    return (
        <>
            <Head title={`Edit ${lead.lead_number}`} />

            <div className="max-w-5xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">
                    Edit {lead.lead_number}
                </h1>

                <form
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.put(`/admin/leads/${lead.public_id}`);
                    }}
                    className="space-y-6"
                >
                    <section className="rounded border p-5">
                        <LeadForm
                            data={form.data}
                            setData={form.setData as any}
                            stages={stages}
                            sources={sources}
                            owners={owners}
                        />
                    </section>

                    <button
                        disabled={form.processing}
                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        Save Changes
                    </button>
                </form>
            </div>
        </>
    );
}
