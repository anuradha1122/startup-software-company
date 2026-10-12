import { Head, useForm } from '@inertiajs/react';

export default function CreateProposal({ versions }: any) {
    const form = useForm({
        requirement_version_id: '',
        estimate_id: '',
        title: '',
        executive_summary: '',
        solution_overview: '',
        delivery_approach: '',
        commercial_notes: '',
        valid_until: '',
    });

    const selectedVersion = versions.find(
        (item: any) =>
            String(item.id) === String(form.data.requirement_version_id),
    );

    return (
        <>
            <Head title="New Proposal" />

            <div className="max-w-5xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">New Proposal</h1>

                <form
                    className="space-y-5"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/admin/proposals');
                    }}
                >
                    <select
                        className="w-full rounded border p-2"
                        value={form.data.requirement_version_id}
                        onChange={(e) => {
                            form.setData(
                                'requirement_version_id',
                                e.target.value,
                            );

                            form.setData('estimate_id', '');
                        }}
                    >
                        <option value="">Select approved requirement</option>

                        {versions.map((version: any) => (
                            <option key={version.id} value={version.id}>
                                {version.requirement.requirement_number}
                                {' — '}
                                {version.requirement.title}
                                {' — v'}
                                {version.version_number}
                            </option>
                        ))}
                    </select>

                    <select
                        className="w-full rounded border p-2"
                        value={form.data.estimate_id}
                        onChange={(e) =>
                            form.setData('estimate_id', e.target.value)
                        }
                    >
                        <option value="">Select approved estimate</option>

                        {selectedVersion?.estimates?.map((estimate: any) => (
                            <option key={estimate.id} value={estimate.id}>
                                {estimate.estimate_number}
                                {' — '}
                                {estimate.currency} {estimate.estimated_revenue}
                            </option>
                        ))}
                    </select>

                    <input
                        className="w-full rounded border p-2"
                        placeholder="Proposal title"
                        value={form.data.title}
                        onChange={(e) => form.setData('title', e.target.value)}
                    />

                    {[
                        ['executive_summary', 'Executive Summary'],
                        ['solution_overview', 'Solution Overview'],
                        ['delivery_approach', 'Delivery Approach'],
                        ['commercial_notes', 'Commercial Notes'],
                    ].map(([field, label]) => (
                        <label key={field} className="block space-y-1">
                            <span>{label}</span>

                            <textarea
                                className="min-h-32 w-full rounded border p-2"
                                value={(form.data as any)[field]}
                                onChange={(e) =>
                                    form.setData(field as any, e.target.value)
                                }
                            />
                        </label>
                    ))}

                    <label className="block space-y-1">
                        <span>Valid Until</span>

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={form.data.valid_until}
                            onChange={(e) =>
                                form.setData('valid_until', e.target.value)
                            }
                        />
                    </label>

                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                        Create Proposal
                    </button>
                </form>
            </div>
        </>
    );
}
