import { Head, router, useForm } from '@inertiajs/react';

type RateForm = {
    currency: string;
    hourly_cost_rate: number | '';
    effective_from: string;
    effective_to: string;
    notes: string;
};

export default function CostRates({ person, rates, canManage }: any) {
    const form = useForm<RateForm>({
        currency: 'LKR',
        hourly_cost_rate: '',
        effective_from: '',
        effective_to: '',
        notes: '',
    });

    return (
        <>
            <Head title={`Cost Rates - ${person.name}`} />

            <div className="max-w-6xl space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">
                        Internal Cost Rates
                    </h1>

                    <p>{person.name}</p>

                    <p className="text-sm text-muted-foreground">
                        Internal project costing only.
                    </p>
                </div>

                {canManage && (
                    <form
                        className="grid gap-3 rounded border p-5 md:grid-cols-3"
                        onSubmit={(event) => {
                            event.preventDefault();

                            form.post(
                                `/admin/people/${person.public_id}/cost-rates`,
                            );
                        }}
                    >
                        <input
                            maxLength={3}
                            className="rounded border p-2"
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
                            step="0.01"
                            className="rounded border p-2"
                            placeholder="Hourly cost rate"
                            value={form.data.hourly_cost_rate}
                            onChange={(e) =>
                                form.setData(
                                    'hourly_cost_rate',
                                    e.target.value
                                        ? Number(e.target.value)
                                        : '',
                                )
                            }
                        />

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={form.data.effective_from}
                            onChange={(e) =>
                                form.setData('effective_from', e.target.value)
                            }
                        />

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={form.data.effective_to}
                            onChange={(e) =>
                                form.setData('effective_to', e.target.value)
                            }
                        />

                        <input
                            className="rounded border p-2"
                            placeholder="Notes"
                            value={form.data.notes}
                            onChange={(e) =>
                                form.setData('notes', e.target.value)
                            }
                        />

                        <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                            Add Rate
                        </button>
                    </form>
                )}

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Period</th>
                                <th className="p-3 text-left">Currency</th>
                                <th className="p-3 text-left">Hourly Cost</th>
                                <th className="p-3 text-left">Status</th>
                                <th className="p-3 text-left">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            {rates.map((rate: any) => (
                                <tr key={rate.public_id} className="border-t">
                                    <td className="p-3">
                                        {rate.effective_from}
                                        {' → '}
                                        {rate.effective_to ?? 'Open'}
                                    </td>

                                    <td className="p-3">{rate.currency}</td>

                                    <td className="p-3">
                                        {rate.hourly_cost_rate}
                                    </td>

                                    <td className="p-3">
                                        {rate.active ? 'Active' : 'Inactive'}
                                    </td>

                                    <td className="p-3">
                                        {canManage && rate.active && (
                                            <button
                                                onClick={() =>
                                                    router.post(
                                                        `/admin/cost-rates/${rate.public_id}/deactivate`,
                                                    )
                                                }
                                                className="underline"
                                            >
                                                Deactivate
                                            </button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
