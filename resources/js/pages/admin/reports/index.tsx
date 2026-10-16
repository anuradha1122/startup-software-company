import { Head, Link } from '@inertiajs/react';

type ReportDefinition = {
    key: string;
    module: string;
    title: string;
    description: string;
    excel_url: string | null;
    pdf_url: string | null;
};

type RecentRun = {
    public_id: string;
    report_type: string;
    format: string;
    status: string;
    download_name: string | null;
    generated_at: string;
    generated_by: string | null;
};

type Props = {
    reports: ReportDefinition[];
    recentRuns: RecentRun[];
    canViewRuns: boolean;
    canExport: boolean;
};

export default function ReportCenter({
    reports,
    recentRuns,
    canViewRuns,
    canExport,
}: Props) {
    return (
        <>
            <Head title="Reports" />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Report Center
                        </h1>

                        <p className="text-sm text-muted-foreground">
                            Internal reports and exports across IT Hub modules.
                        </p>
                    </div>

                    {canViewRuns && (
                        <Link
                            href="/admin/reports/runs"
                            className="rounded border px-4 py-2"
                        >
                            Report History
                        </Link>
                    )}
                </div>

                <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    {reports.map((report) => (
                        <div
                            key={report.key}
                            className="space-y-4 rounded border p-5"
                        >
                            <div>
                                <p className="text-xs text-muted-foreground">
                                    {report.module}
                                </p>

                                <h2 className="text-lg font-semibold">
                                    {report.title}
                                </h2>

                                <p className="mt-1 text-sm text-muted-foreground">
                                    {report.description}
                                </p>
                            </div>

                            {canExport && (
                                <div className="flex flex-wrap gap-2">
                                    {report.excel_url && (
                                        <a
                                            href={report.excel_url}
                                            className="rounded border px-3 py-2 text-sm"
                                        >
                                            Excel
                                        </a>
                                    )}

                                    {report.pdf_url && (
                                        <a
                                            href={report.pdf_url}
                                            className="rounded border px-3 py-2 text-sm"
                                        >
                                            PDF
                                        </a>
                                    )}
                                </div>
                            )}
                        </div>
                    ))}
                </section>

                {canViewRuns && (
                    <section className="rounded border p-5">
                        <div className="mb-4 flex justify-between gap-3">
                            <h2 className="text-lg font-semibold">
                                Recent M13 Report Runs
                            </h2>

                            <Link
                                href="/admin/reports/runs"
                                className="text-sm underline"
                            >
                                View All
                            </Link>
                        </div>

                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr>
                                        <th className="p-2 text-left">
                                            Report
                                        </th>

                                        <th className="p-2 text-left">
                                            Format
                                        </th>

                                        <th className="p-2 text-left">
                                            Status
                                        </th>

                                        <th className="p-2 text-left">
                                            Generated By
                                        </th>

                                        <th className="p-2 text-left">
                                            Generated At
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {recentRuns.map((run) => (
                                        <tr
                                            key={run.public_id}
                                            className="border-t"
                                        >
                                            <td className="p-2">
                                                {run.report_type}
                                            </td>

                                            <td className="p-2">
                                                {run.format}
                                            </td>

                                            <td className="p-2">
                                                {run.status}
                                            </td>

                                            <td className="p-2">
                                                {run.generated_by ?? '-'}
                                            </td>

                                            <td className="p-2">
                                                {run.generated_at}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </section>
                )}
            </div>
        </>
    );
}
