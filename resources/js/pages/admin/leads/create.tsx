import LeadForm from '@/components/m01/lead-form';
import { Head, useForm } from '@inertiajs/react';

export default function CreateLead({ stages, sources, owners }: any) {
    const form = useForm({
        title: '',
        company_name: '',
        stage_id: stages[0]?.id?.toString() ?? '',
        source_id: '',
        owner_id: '',
        estimated_value: '',
        currency: 'LKR',
        priority: 'medium',
        next_follow_up_at: '',
        notes: '',

        contact: {
            name: '',
            designation: '',
            email: '',
            phone: '',
        },
    });

    return (
        <>
            <Head title="Create Lead" />

            <div className="max-w-5xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">Create Lead</h1>

                <form
                    className="space-y-6"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/admin/leads');
                    }}
                >
                    <section className="rounded-lg border p-5">
                        <h2 className="mb-4 text-lg font-medium">
                            Lead information
                        </h2>

                        <LeadForm
                            data={form.data}
                            setData={form.setData as any}
                            stages={stages}
                            sources={sources}
                            owners={owners}
                        />
                    </section>

                    <section className="rounded-lg border p-5">
                        <h2 className="mb-4 text-lg font-medium">
                            Primary Contact
                        </h2>

                        <div className="grid gap-4 md:grid-cols-2">
                            {[
                                ['name', 'Name'],
                                ['designation', 'Designation'],
                                ['email', 'Email'],
                                ['phone', 'Phone'],
                            ].map(([key, label]) => (
                                <label key={key} className="space-y-1">
                                    <span>{label}</span>

                                    <input
                                        className="w-full rounded border p-2"
                                        value={(form.data.contact as any)[key]}
                                        onChange={(e) =>
                                            form.setData('contact', {
                                                ...form.data.contact,

                                                [key]: e.target.value,
                                            })
                                        }
                                    />
                                </label>
                            ))}
                        </div>
                    </section>

                    <button
                        disabled={form.processing}
                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        Create Lead
                    </button>
                </form>
            </div>
        </>
    );
}
