import { Head, Link, router, useForm } from '@inertiajs/react';

type Financials = {
    currency: string;
    contracted_revenue: number;
    invoiced_revenue: number;
    collected_cash: number;
    labor_cost: number;
    non_labor_cost: number;
    actual_cost: number;
    gross_profit: number;
    gross_margin_percent: number | null;
    unbilled_revenue: number;
    outstanding_receivable: number;
    collection_percent: number | null;
    cost_consumption_percent: number | null;
    invoice_progress_percent: number | null;
    currency_mismatch_count: number;
    currency_mismatches: {
        source: string;
        count: number;
        message: string;
    }[];
};

type Snapshot = {
    id: number;
    public_id: string;
    snapshot_date: string;
    currency: string;
    gross_profit: string;
    gross_margin_percent: string | null;
    actual_cost: string;
    invoiced_revenue: string;
    collected_cash: string;
};

type FinanceSetting = {
    target_margin_percent: string;
    warning_margin_percent: string;
    alerts_enabled: boolean;
};

type Props = {
    project: {
        public_id: string;
        project_number: string;
        name: string;
        client: {
            name: string;
        };
    };
    financials: Financials;
    financeSetting: FinanceSetting;
    snapshots: Snapshot[];
    canManageSettings: boolean;
    canSnapshot: boolean;
    canExport: boolean;
};

type SettingForm = {
    target_margin_percent: number;
    warning_margin_percent: number;
    alerts_enabled: boolean;
};

const money = (currency: string, value: number) =>
    `${currency} ${Number(value).toFixed(2)}`;

const percentage = (value: number | null) =>
    value === null ? 'N/A' : `${Number(value).toFixed(2)}%`;

export default function ProfitabilityShow({
    project,
    financials,
    financeSetting,
    snapshots,
    canManageSettings,
    canSnapshot,
    canExport,
}: Props) {
    const form = useForm<SettingForm>({
        target_margin_percent: Number(financeSetting.target_margin_percent),

        warning_margin_percent: Number(financeSetting.warning_margin_percent),

        alerts_enabled: financeSetting.alerts_enabled,
    });

    return (
        <>
            <Head title={`${project.project_number} Profitability`} />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Project Profitability
                        </h1>

                        <p>
                            {project.project_number}
                            {' — '}
                            {project.name}
                        </p>

                        <p className="text-sm text-muted-foreground">
                            {project.client.name}
                        </p>
                    </div>

                    <div className="flex flex-wrap gap-2">
                        {canExport && (
                            <>
                                <a
                                    href={`/admin/projects/${project.public_id}/profitability/export/excel`}
                                    className="rounded border px-3 py-2"
                                >
                                    Excel
                                </a>

                                <a
                                    href={`/admin/projects/${project.public_id}/profitability/export/pdf`}
                                    className="rounded border px-3 py-2"
                                >
                                    PDF
                                </a>
                            </>
                        )}

                        {canSnapshot &&
                            financials.currency_mismatch_count === 0 && (
                                <button
                                    type="button"
                                    onClick={() =>
                                        router.post(
                                            `/admin/projects/${project.public_id}/profitability/snapshot`,
                                        )
                                    }
                                    className="rounded border px-3 py-2"
                                >
                                    Capture Snapshot
                                </button>
                            )}

                        <Link
                            href={`/admin/projects/${project.public_id}`}
                            className="rounded border px-3 py-2"
                        >
                            Project
                        </Link>
                    </div>
                </div>

                {financials.currency_mismatch_count > 0 && (
                    <section className="rounded border p-4">
                        <h2 className="font-semibold">
                            Currency Integrity Warning
                        </h2>

                        <p className="mt-1 text-sm text-muted-foreground">
                            Financial snapshots are disabled until these
                            mismatches are resolved.
                        </p>

                        <div className="mt-3 space-y-2">
                            {financials.currency_mismatches.map((warning) => (
                                <p key={warning.source} className="text-sm">
                                    {warning.message} ({warning.count})
                                </p>
                            ))}
                        </div>
                    </section>
                )}

                <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <Metric
                        label="Contracted Revenue"
                        value={money(
                            financials.currency,
                            financials.contracted_revenue,
                        )}
                    />

                    <Metric
                        label="Invoiced Revenue"
                        value={money(
                            financials.currency,
                            financials.invoiced_revenue,
                        )}
                    />

                    <Metric
                        label="Collected Cash"
                        value={money(
                            financials.currency,
                            financials.collected_cash,
                        )}
                    />

                    <Metric
                        label="Outstanding Receivable"
                        value={money(
                            financials.currency,
                            financials.outstanding_receivable,
                        )}
                    />

                    <Metric
                        label="Labor Cost"
                        value={money(
                            financials.currency,
                            financials.labor_cost,
                        )}
                    />

                    <Metric
                        label="Non-Labor Cost"
                        value={money(
                            financials.currency,
                            financials.non_labor_cost,
                        )}
                    />

                    <Metric
                        label="Actual Cost"
                        value={money(
                            financials.currency,
                            financials.actual_cost,
                        )}
                    />

                    <Metric
                        label="Gross Profit"
                        value={money(
                            financials.currency,
                            financials.gross_profit,
                        )}
                    />

                    <Metric
                        label="Gross Margin"
                        value={percentage(financials.gross_margin_percent)}
                    />

                    <Metric
                        label="Unbilled Revenue"
                        value={money(
                            financials.currency,
                            financials.unbilled_revenue,
                        )}
                    />

                    <Metric
                        label="Collection"
                        value={percentage(financials.collection_percent)}
                    />

                    <Metric
                        label="Cost Consumption"
                        value={percentage(financials.cost_consumption_percent)}
                    />
                </section>

                {canManageSettings && (
                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">
                            Finance Settings
                        </h2>

                        <form
                            className="grid gap-4 md:grid-cols-3"
                            onSubmit={(event) => {
                                event.preventDefault();

                                form.put(
                                    `/admin/projects/${project.public_id}/profitability/settings`,
                                    {
                                        preserveScroll: true,
                                    },
                                );
                            }}
                        >
                            <label className="space-y-1">
                                <span className="text-sm">Target Margin %</span>

                                <input
                                    type="number"
                                    step="0.01"
                                    value={form.data.target_margin_percent}
                                    onChange={(event) =>
                                        form.setData(
                                            'target_margin_percent',
                                            Number(event.target.value),
                                        )
                                    }
                                    className="w-full rounded border p-2"
                                />
                            </label>

                            <label className="space-y-1">
                                <span className="text-sm">
                                    Warning Margin %
                                </span>

                                <input
                                    type="number"
                                    step="0.01"
                                    value={form.data.warning_margin_percent}
                                    onChange={(event) =>
                                        form.setData(
                                            'warning_margin_percent',
                                            Number(event.target.value),
                                        )
                                    }
                                    className="w-full rounded border p-2"
                                />
                            </label>

                            <label className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={form.data.alerts_enabled}
                                    onChange={(event) =>
                                        form.setData(
                                            'alerts_enabled',
                                            event.target.checked,
                                        )
                                    }
                                />
                                Margin Alerts
                            </label>

                            <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                                Save Settings
                            </button>
                        </form>
                    </section>
                )}

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Snapshot History
                    </h2>

                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr>
                                    <th className="p-2 text-left">Date</th>

                                    <th className="p-2 text-left">Revenue</th>

                                    <th className="p-2 text-left">Cost</th>

                                    <th className="p-2 text-left">Profit</th>

                                    <th className="p-2 text-left">Margin</th>

                                    <th className="p-2 text-left">Collected</th>
                                </tr>
                            </thead>

                            <tbody>
                                {snapshots.map((snapshot) => (
                                    <tr
                                        key={snapshot.public_id}
                                        className="border-t"
                                    >
                                        <td className="p-2">
                                            {snapshot.snapshot_date}
                                        </td>

                                        <td className="p-2">
                                            {snapshot.currency}{' '}
                                            {snapshot.invoiced_revenue}
                                        </td>

                                        <td className="p-2">
                                            {snapshot.currency}{' '}
                                            {snapshot.actual_cost}
                                        </td>

                                        <td className="p-2">
                                            {snapshot.currency}{' '}
                                            {snapshot.gross_profit}
                                        </td>

                                        <td className="p-2">
                                            {snapshot.gross_margin_percent ??
                                                'N/A'}
                                        </td>

                                        <td className="p-2">
                                            {snapshot.currency}{' '}
                                            {snapshot.collected_cash}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </>
    );
}

function Metric({ label, value }: { label: string; value: string }) {
    return (
        <div className="rounded border p-4">
            <p className="text-sm text-muted-foreground">{label}</p>

            <p className="mt-1 text-xl font-semibold">{value}</p>
        </div>
    );
}
