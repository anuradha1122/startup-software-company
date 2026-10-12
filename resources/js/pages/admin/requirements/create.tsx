import RequirementFields from '@/components/m03/requirement-fields';
import { Head, useForm } from '@inertiajs/react';

export default function CreateRequirement({ clients, owners }: any) {
    const form = useForm({
        client_id: '',
        title: '',
        owner_id: '',
        objective: '',
        scope_in: '',
        scope_out: '',

        functional_requirements: [''],

        non_functional_requirements: [''],

        assumptions: [''],
        constraints: [''],
        acceptance_criteria: [''],

        client_notes: '',
        internal_notes: '',
    });

    return (
        <>
            <Head title="Create Requirement" />

            <div className="max-w-6xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">
                    Create Requirement
                </h1>

                <form
                    className="space-y-6"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/admin/requirements');
                    }}
                >
                    <div className="grid gap-4 rounded border p-5 md:grid-cols-2">
                        <label className="space-y-1">
                            <span>Client</span>

                            <select
                                className="w-full rounded border p-2"
                                value={form.data.client_id}
                                onChange={(e) =>
                                    form.setData('client_id', e.target.value)
                                }
                            >
                                <option value="">Select Client</option>

                                {clients.map((client: any) => (
                                    <option key={client.id} value={client.id}>
                                        {client.client_number}
                                        {' — '}
                                        {client.name}
                                    </option>
                                ))}
                            </select>
                        </label>

                        <label className="space-y-1">
                            <span>Owner</span>

                            <select
                                className="w-full rounded border p-2"
                                value={form.data.owner_id}
                                onChange={(e) =>
                                    form.setData('owner_id', e.target.value)
                                }
                            >
                                <option value="">Unassigned</option>

                                {owners.map((owner: any) => (
                                    <option key={owner.id} value={owner.id}>
                                        {owner.name}
                                    </option>
                                ))}
                            </select>
                        </label>

                        <label className="space-y-1 md:col-span-2">
                            <span>Requirement Title</span>

                            <input
                                className="w-full rounded border p-2"
                                value={form.data.title}
                                onChange={(e) =>
                                    form.setData('title', e.target.value)
                                }
                            />
                        </label>
                    </div>

                    <div className="rounded border p-5">
                        <RequirementFields
                            data={form.data}
                            setData={form.setData as any}
                        />
                    </div>

                    <button
                        disabled={form.processing}
                        className="rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        Create Requirement
                    </button>
                </form>
            </div>
        </>
    );
}
