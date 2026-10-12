import { Head, useForm } from '@inertiajs/react';

export default function CreateContract({ quotation }: any) {
    const form = useForm({
        quotation_id: quotation.id,

        title: `Software Services Contract - ${quotation.client.name}`,

        start_date: '',
        end_date: '',

        payment_terms_days: quotation.payment_terms_days ?? 30,

        payment_schedule: quotation.payment_schedule ?? '',

        special_terms: '',
        termination_terms: '',
    });

    return (
        <>
            <Head title="Create Contract" />

            <div className="max-w-5xl p-6">
                <h1 className="mb-6 text-2xl font-semibold">Create Contract</h1>

                <p className="mb-5">
                    From quotation {quotation.quotation_number}
                </p>

                <form
                    className="space-y-4"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/admin/contracts');
                    }}
                >
                    <input
                        className="w-full rounded border p-2"
                        value={form.data.title}
                        onChange={(e) => form.setData('title', e.target.value)}
                    />

                    <div className="grid gap-4 md:grid-cols-2">
                        <input
                            type="date"
                            className="rounded border p-2"
                            value={form.data.start_date}
                            onChange={(e) =>
                                form.setData('start_date', e.target.value)
                            }
                        />

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={form.data.end_date}
                            onChange={(e) =>
                                form.setData('end_date', e.target.value)
                            }
                        />
                    </div>

                    <input
                        type="number"
                        className="w-full rounded border p-2"
                        value={form.data.payment_terms_days}
                        onChange={(e) =>
                            form.setData(
                                'payment_terms_days',
                                Number(e.target.value),
                            )
                        }
                    />

                    <textarea
                        className="min-h-28 w-full rounded border p-2"
                        placeholder="Payment schedule"
                        value={form.data.payment_schedule}
                        onChange={(e) =>
                            form.setData('payment_schedule', e.target.value)
                        }
                    />

                    <textarea
                        className="min-h-28 w-full rounded border p-2"
                        placeholder="Special terms"
                        value={form.data.special_terms}
                        onChange={(e) =>
                            form.setData('special_terms', e.target.value)
                        }
                    />

                    <textarea
                        className="min-h-28 w-full rounded border p-2"
                        placeholder="Termination terms"
                        value={form.data.termination_terms}
                        onChange={(e) =>
                            form.setData('termination_terms', e.target.value)
                        }
                    />

                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                        Create Contract
                    </button>
                </form>
            </div>
        </>
    );
}
