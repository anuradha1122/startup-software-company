import { Head, Link } from '@inertiajs/react';

type ProjectRow = {
    project: {
        public_id: string;
        project_number: string;
        name: string;
        client: {
            name: string;
        };
    };
    financials: {
        currency: string;
        contracted_revenue: number;
        invoiced_revenue: number;
        collected_cash: number;
        actual_cost: number;
        gross_profit: number;
        gross_margin_percent: number | null;
        outstanding_receivable: number;
        currency_mismatch_count: number;
    };
};

type CurrencySummary = {
    currency: string;
    project_count: number;
    contracted_revenue: number;
    invoiced_revenue: number;
    collected_cash: number;
    actual_cost: number;
    gross_profit: number;
    outstanding_receivable: number;
};

type Props = {
    projects: ProjectRow[];
    byCurrency: CurrencySummary[];
};

const number = (value: number) => Number(value).toFixed(2);

export default function FinanceDashboard({ projects, byCurrency }: Props) {
    return (
        <>
            <Head title="Finance" />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Finance Portfolio
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            Revenue, cost, profitability and collection by
                            project.
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href="/admin/finance/export/excel"
                            className="rounded border px-3 py-2"
                        >
                            Excel
                        </a>

                        <a
                            href="/admin/finance/export/pdf"
                            className="rounded border px-3 py-2"
                        >
                            PDF
                        </a>
                    </div>
                </div>

                <section className="space-y-4">
                    <h2 className="text-lg font-semibold">
                        Currency Summaries
                    </h2>

                    <div className="grid gap-4 xl:grid-cols-2">
                        {byCurrency.map((summary) => (
                            <div
                                key={summary.currency}
                                className="rounded border p-5"
                            >
                                <h3 className="text-xl font-semibold">
                                    {summary.currency}
                                </h3>

                                <p className="text-sm text-muted-foreground">
                                    {summary.project_count} projects
                                </p>

                                <div className="mt-4 grid gap-3 md:grid-cols-2">
                                    <Metric
                                        label="Contracted"
                                        value={number(
                                            summary.contracted_revenue,
                                        )}
                                    />

                                    <Metric
                                        label="Invoiced"
                                        value={number(summary.invoiced_revenue)}
                                    />

                                    <Metric
                                        label="Collected"
                                        value={number(summary.collected_cash)}
                                    />

                                    <Metric
                                        label="Actual Cost"
                                        value={number(summary.actual_cost)}
                                    />

                                    <Metric
                                        label="Gross Profit"
                                        value={number(summary.gross_profit)}
                                    />

                                    <Metric
                                        label="Receivable"
                                        value={number(
                                            summary.outstanding_receivable,
                                        )}
                                    />
                                </div>
                            </div>
                        ))}
                    </div>
                </section>

                <section className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Project</th>

                                <th className="p-3 text-left">Client</th>

                                <th className="p-3 text-left">Currency</th>

                                <th className="p-3 text-right">Contract</th>

                                <th className="p-3 text-right">Invoiced</th>

                                <th className="p-3 text-right">Collected</th>

                                <th className="p-3 text-right">Cost</th>

                                <th className="p-3 text-right">Profit</th>

                                <th className="p-3 text-right">Margin</th>

                                <th className="p-3 text-right">Receivable</th>
                            </tr>
                        </thead>

                        <tbody>
                            {projects.map((row) => (
                                <tr
                                    key={row.project.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/projects/${row.project.public_id}/profitability`}
                                            className="font-medium underline"
                                        >
                                            {row.project.project_number}
                                            {' — '}
                                            {row.project.name}
                                        </Link>
                                    </td>

                                    <td className="p-3">
                                        {row.project.client.name}
                                    </td>

                                    <td className="p-3">
                                        {row.financials.currency}
                                    </td>

                                    <td className="p-3 text-right">
                                        {number(
                                            row.financials.contracted_revenue,
                                        )}
                                    </td>

                                    <td className="p-3 text-right">
                                        {number(
                                            row.financials.invoiced_revenue,
                                        )}
                                    </td>

                                    <td className="p-3 text-right">
                                        {number(row.financials.collected_cash)}
                                    </td>

                                    <td className="p-3 text-right">
                                        {number(row.financials.actual_cost)}
                                    </td>

                                    <td className="p-3 text-right">
                                        {number(row.financials.gross_profit)}
                                    </td>

                                    <td className="p-3 text-right">
                                        {row.financials.gross_margin_percent !==
                                        null
                                            ? `${number(
                                                  row.financials
                                                      .gross_margin_percent,
                                              )}%`
                                            : 'N/A'}
                                    </td>

                                    <td className="p-3 text-right">
                                        {number(
                                            row.financials
                                                .outstanding_receivable,
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </section>
            </div>
        </>
    );
}

function Metric({ label, value }: { label: string; value: string }) {
    return (
        <div>
            <p className="text-xs text-muted-foreground">{label}</p>

            <p className="font-semibold">{value}</p>
        </div>
    );
}
