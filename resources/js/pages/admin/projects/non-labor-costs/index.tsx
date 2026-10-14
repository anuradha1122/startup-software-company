import { Head, Link } from '@inertiajs/react';

export default function NonLaborCosts({ project, expenses, summary }: any) {
    return (
        <>
            <Head title={`${project.project_number} Non-Labor Cost`} />

            <div className="space-y-6 p-6">
                <div className="flex justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Non-Labor Cost
                        </h1>

                        <p>
                            {project.project_number}
                            {' — '}
                            {project.name}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/projects/${project.public_id}/non-labor-costs/export/excel`}
                            className="rounded border px-3 py-2"
                        >
                            Excel
                        </a>

                        <a
                            href={`/admin/projects/${project.public_id}/non-labor-costs/export/pdf`}
                            className="rounded border px-3 py-2"
                        >
                            PDF
                        </a>

                        <Link
                            href={`/admin/projects/${project.public_id}`}
                            className="rounded border px-3 py-2"
                        >
                            Project
                        </Link>
                    </div>
                </div>

                <section className="grid gap-4 md:grid-cols-2">
                    <div className="rounded border p-4">
                        <span className="text-sm text-muted-foreground">
                            Approved Expenses
                        </span>

                        <p className="text-2xl font-semibold">
                            {summary.approved_count}
                        </p>
                    </div>

                    <div className="rounded border p-4">
                        <span className="text-sm text-muted-foreground">
                            Non-Labor Cost
                        </span>

                        <p className="text-2xl font-semibold">
                            {Number(summary.total_non_labor_cost).toFixed(2)}
                        </p>
                    </div>
                </section>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Expense</th>

                                <th className="p-3 text-left">Date</th>

                                <th className="p-3 text-left">Supplier</th>

                                <th className="p-3 text-left">Title</th>

                                <th className="p-3 text-left">Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            {expenses.map((expense: any) => (
                                <tr
                                    key={expense.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        {expense.expense_number}
                                    </td>

                                    <td className="p-3">
                                        {expense.expense_date}
                                    </td>

                                    <td className="p-3">
                                        {expense.supplier?.name ?? '-'}
                                    </td>

                                    <td className="p-3">{expense.title}</td>

                                    <td className="p-3">
                                        {expense.currency}{' '}
                                        {expense.total_amount}
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
