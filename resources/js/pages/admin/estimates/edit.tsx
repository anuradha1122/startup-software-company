import { Head, router, useForm } from '@inertiajs/react';

type EstimateLine = {
    module_name: string;
    description: string;
    estimated_hours: number;
    billing_rate: number;
    cost_rate: number;
};

type EstimateFormData = {
    currency: string;
    contingency_percent: number;
    assumptions: string;
    internal_notes: string;
    lines: EstimateLine[];
};

export default function EditEstimate({ estimate, requirement, version }: any) {
    const form = useForm<EstimateFormData>({
        currency: estimate?.currency ?? 'LKR',

        contingency_percent: Number(estimate?.contingency_percent ?? 10),

        assumptions: estimate?.assumptions ?? '',

        internal_notes: estimate?.internal_notes ?? '',

        lines: estimate?.lines?.length
            ? estimate.lines.map((line: any) => ({
                  module_name: line.module_name,

                  description: line.description ?? '',

                  estimated_hours: Number(line.estimated_hours),

                  billing_rate: Number(line.billing_rate),

                  cost_rate: Number(line.cost_rate),
              }))
            : [
                  {
                      module_name: '',
                      description: '',
                      estimated_hours: 0,
                      billing_rate: 0,
                      cost_rate: 0,
                  },
              ],
    });

    const updateLine = (
        index: number,
        field: keyof EstimateLine,
        value: string | number,
    ) => {
        const lines = [...form.data.lines];

        lines[index] = {
            ...lines[index],
            [field]: value,
        };

        form.setData('lines', lines);
    };

    const save = () => {
        if (estimate) {
            form.put(`/admin/estimates/${estimate.public_id}`);

            return;
        }

        form.post(
            `/admin/requirements/${requirement.public_id}/versions/${version.public_id}/estimates`,
        );
    };

    return (
        <>
            <Head
                title={estimate ? estimate.estimate_number : 'New Estimate'}
            />

            <div className="max-w-7xl p-6">
                <div className="mb-6 flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            {estimate
                                ? estimate.estimate_number
                                : 'New Estimate'}
                        </h1>

                        <p>
                            {requirement.requirement_number}
                            {' — '}
                            Version {version.version_number}
                        </p>
                    </div>

                    {estimate && (
                        <div className="flex gap-2">
                            <a
                                href={`/admin/estimates/${estimate.public_id}/excel`}
                                className="rounded border px-3 py-2"
                            >
                                Excel
                            </a>

                            <a
                                href={`/admin/estimates/${estimate.public_id}/pdf`}
                                className="rounded border px-3 py-2"
                            >
                                PDF
                            </a>

                            {estimate.status === 'draft' && (
                                <button
                                    onClick={() =>
                                        router.post(
                                            `/admin/estimates/${estimate.public_id}/approve`,
                                        )
                                    }
                                    className="rounded border px-3 py-2"
                                >
                                    Approve Estimate
                                </button>
                            )}
                        </div>
                    )}
                </div>

                <div className="grid gap-4 md:grid-cols-3">
                    <label className="space-y-1">
                        <span>Currency</span>

                        <input
                            className="w-full rounded border p-2"
                            value={form.data.currency}
                            onChange={(e) =>
                                form.setData(
                                    'currency',
                                    e.target.value.toUpperCase(),
                                )
                            }
                        />
                    </label>

                    <label className="space-y-1">
                        <span>Contingency %</span>

                        <input
                            type="number"
                            min="0"
                            max="100"
                            step="0.01"
                            className="w-full rounded border p-2"
                            value={form.data.contingency_percent}
                            onChange={(e) =>
                                form.setData(
                                    'contingency_percent',
                                    Number(e.target.value),
                                )
                            }
                        />
                    </label>
                </div>

                <div className="mt-6 overflow-x-auto rounded border">
                    <table className="w-full min-w-250 text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-2">Module</th>

                                <th className="p-2">Description</th>

                                <th className="p-2">Hours</th>

                                <th className="p-2">Billing Rate</th>

                                <th className="p-2">Cost Rate</th>

                                <th className="p-2" />
                            </tr>
                        </thead>

                        <tbody>
                            {form.data.lines.map((line, index) => (
                                <tr key={index} className="border-t">
                                    <td className="p-2">
                                        <input
                                            className="w-full rounded border p-2"
                                            value={line.module_name}
                                            onChange={(e) =>
                                                updateLine(
                                                    index,
                                                    'module_name',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </td>

                                    <td className="p-2">
                                        <input
                                            className="w-full rounded border p-2"
                                            value={line.description}
                                            onChange={(e) =>
                                                updateLine(
                                                    index,
                                                    'description',
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </td>

                                    {(
                                        [
                                            'estimated_hours',
                                            'billing_rate',
                                            'cost_rate',
                                        ] as const
                                    ).map((field) => (
                                        <td key={field} className="p-2">
                                            <input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                className="w-full rounded border p-2"
                                                value={line[field]}
                                                onChange={(e) =>
                                                    updateLine(
                                                        index,
                                                        field,
                                                        Number(e.target.value),
                                                    )
                                                }
                                            />
                                        </td>
                                    ))}

                                    <td className="p-2">
                                        <button
                                            type="button"
                                            onClick={() =>
                                                form.setData(
                                                    'lines',
                                                    form.data.lines.filter(
                                                        (_, i) => i !== index,
                                                    ),
                                                )
                                            }
                                        >
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <button
                    type="button"
                    className="mt-3 rounded border px-3 py-2"
                    onClick={() =>
                        form.setData('lines', [
                            ...form.data.lines,

                            {
                                module_name: '',

                                description: '',

                                estimated_hours: 0,

                                billing_rate: 0,

                                cost_rate: 0,
                            },
                        ])
                    }
                >
                    Add Estimate Line
                </button>

                <div className="mt-6 grid gap-4 md:grid-cols-2">
                    <label className="space-y-1">
                        <span>Assumptions</span>

                        <textarea
                            className="min-h-32 w-full rounded border p-2"
                            value={form.data.assumptions}
                            onChange={(e) =>
                                form.setData('assumptions', e.target.value)
                            }
                        />
                    </label>

                    <label className="space-y-1">
                        <span>Internal Notes</span>

                        <textarea
                            className="min-h-32 w-full rounded border p-2"
                            value={form.data.internal_notes}
                            onChange={(e) =>
                                form.setData('internal_notes', e.target.value)
                            }
                        />
                    </label>
                </div>

                {estimate && (
                    <div className="mt-6 grid gap-4 rounded border p-5 md:grid-cols-5">
                        <div>
                            <span className="text-sm text-muted-foreground">
                                Hours
                            </span>
                            <p className="font-semibold">
                                {estimate.total_hours}
                            </p>
                        </div>

                        <div>
                            <span className="text-sm text-muted-foreground">
                                Revenue
                            </span>
                            <p className="font-semibold">
                                {estimate.estimated_revenue}
                            </p>
                        </div>

                        <div>
                            <span className="text-sm text-muted-foreground">
                                Cost
                            </span>
                            <p className="font-semibold">
                                {estimate.estimated_cost}
                            </p>
                        </div>

                        <div>
                            <span className="text-sm text-muted-foreground">
                                Margin
                            </span>
                            <p className="font-semibold">
                                {estimate.gross_margin}
                            </p>
                        </div>

                        <div>
                            <span className="text-sm text-muted-foreground">
                                Margin %
                            </span>
                            <p className="font-semibold">
                                {estimate.gross_margin_percent}%
                            </p>
                        </div>
                    </div>
                )}

                {(!estimate || estimate.status === 'draft') && (
                    <button
                        type="button"
                        disabled={form.processing}
                        onClick={save}
                        className="mt-6 rounded bg-primary px-4 py-2 text-primary-foreground"
                    >
                        Save Estimate
                    </button>
                )}
            </div>
        </>
    );
}
