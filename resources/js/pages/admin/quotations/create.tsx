import { Head, useForm } from '@inertiajs/react';

export default function CreateQuotation({ versions, proposal }: any) {
    const initialVersionId = proposal?.requirement_version_id
        ? String(proposal.requirement_version_id)
        : '';

    const initialEstimateId = proposal?.estimate_id
        ? String(proposal.estimate_id)
        : '';

    const form = useForm({
        proposal_id: proposal?.id ? String(proposal.id) : '',

        requirement_version_id: initialVersionId,

        estimate_id: initialEstimateId,

        currency: 'LKR',
        discount_percent: 0,
        tax_percent: 0,
        payment_terms_days: 30,
        payment_schedule: '',
        terms_and_conditions: '',
        notes: '',
        valid_until: '',
    });

    const selectedVersion = versions.find(
        (version: any) =>
            String(version.id) === String(form.data.requirement_version_id),
    );

    return (
        <>
            <Head title="New Quotation" />

            <div className="max-w-5xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">New Quotation</h1>

                <form
                    className="space-y-4"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/admin/quotations');
                    }}
                >
                    <select
                        className="w-full rounded border p-2"
                        value={form.data.requirement_version_id}
                        onChange={(e) =>
                            form.setData(
                                'requirement_version_id',
                                e.target.value,
                            )
                        }
                    >
                        <option value="">Approved requirement</option>

                        {versions.map((version: any) => (
                            <option key={version.id} value={version.id}>
                                {version.requirement.requirement_number}
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
                        <option value="">Approved estimate</option>

                        {selectedVersion?.estimates?.map((estimate: any) => (
                            <option key={estimate.id} value={estimate.id}>
                                {estimate.estimate_number}
                            </option>
                        ))}
                    </select>

                    <div className="grid gap-4 md:grid-cols-3">
                        <input
                            className="rounded border p-2"
                            maxLength={3}
                            value={form.data.currency}
                            onChange={(e) =>
                                form.setData(
                                    'currency',
                                    e.target.value.toUpperCase(),
                                )
                            }
                        />

                        <input
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            className="rounded border p-2"
                            placeholder="Discount %"
                            value={form.data.discount_percent}
                            onChange={(e) =>
                                form.setData(
                                    'discount_percent',
                                    Number(e.target.value),
                                )
                            }
                        />

                        <input
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            className="rounded border p-2"
                            placeholder="Tax %"
                            value={form.data.tax_percent}
                            onChange={(e) =>
                                form.setData(
                                    'tax_percent',
                                    Number(e.target.value),
                                )
                            }
                        />
                    </div>

                    <input
                        type="number"
                        className="w-full rounded border p-2"
                        placeholder="Payment terms days"
                        value={form.data.payment_terms_days}
                        onChange={(e) =>
                            form.setData(
                                'payment_terms_days',
                                Number(e.target.value),
                            )
                        }
                    />

                    <textarea
                        className="min-h-24 w-full rounded border p-2"
                        placeholder="Payment schedule"
                        value={form.data.payment_schedule}
                        onChange={(e) =>
                            form.setData('payment_schedule', e.target.value)
                        }
                    />

                    <textarea
                        className="min-h-32 w-full rounded border p-2"
                        placeholder="Terms and conditions"
                        value={form.data.terms_and_conditions}
                        onChange={(e) =>
                            form.setData('terms_and_conditions', e.target.value)
                        }
                    />

                    <input
                        type="date"
                        className="rounded border p-2"
                        value={form.data.valid_until}
                        onChange={(e) =>
                            form.setData('valid_until', e.target.value)
                        }
                    />

                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                        Create Quotation
                    </button>
                </form>
            </div>
        </>
    );
}
