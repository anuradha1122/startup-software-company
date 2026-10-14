import { Head, Link, useForm } from '@inertiajs/react';

type ExpenseForm = {
    supplier_id: string;
    type:
        | 'travel'
        | 'software'
        | 'hosting'
        | 'equipment'
        | 'supplier'
        | 'professional_service'
        | 'communication'
        | 'other';
    title: string;
    description: string;
    expense_date: string;
    reference_number: string;
    currency: string;
    subtotal: number | '';
    tax_amount: number | '';
    billable_to_client: boolean;
    incurred_by: string;
    attachments: File[];
};

export default function ProjectExpenses({
    project,
    expenses,
    suppliers,
    users,
}: any) {
    const form = useForm<ExpenseForm>({
        supplier_id: '',
        type: 'other',
        title: '',
        description: '',
        expense_date: '',
        reference_number: '',
        currency: 'LKR',
        subtotal: '',
        tax_amount: 0,
        billable_to_client: false,
        incurred_by: '',
        attachments: [],
    });

    return (
        <>
            <Head title={`${project.project_number} Expenses`} />

            <div className="space-y-6 p-6">
                <div className="flex flex-wrap justify-between gap-3">
                    <div>
                        <h1 className="text-2xl font-semibold">
                            Project Expenses
                        </h1>

                        <p>
                            {project.project_number}
                            {' — '}
                            {project.name}
                        </p>
                    </div>

                    <div className="flex gap-2">
                        <a
                            href={`/admin/projects/${project.public_id}/expenses/export/excel`}
                            className="rounded border px-3 py-2"
                        >
                            Excel
                        </a>

                        <a
                            href={`/admin/projects/${project.public_id}/expenses/export/pdf`}
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

                {['active', 'on_hold'].includes(project.status) && (
                    <form
                        className="grid gap-3 rounded border p-5 md:grid-cols-3"
                        onSubmit={(event) => {
                            event.preventDefault();

                            form.post(
                                `/admin/projects/${project.public_id}/expenses`,
                                {
                                    forceFormData: true,
                                },
                            );
                        }}
                    >
                        <input
                            className="rounded border p-2 md:col-span-2"
                            placeholder="Expense title"
                            value={form.data.title}
                            onChange={(e) =>
                                form.setData('title', e.target.value)
                            }
                        />

                        <select
                            className="rounded border p-2"
                            value={form.data.type}
                            onChange={(e) =>
                                form.setData(
                                    'type',
                                    e.target.value as ExpenseForm['type'],
                                )
                            }
                        >
                            <option value="travel">Travel</option>

                            <option value="software">Software</option>

                            <option value="hosting">Hosting</option>

                            <option value="equipment">Equipment</option>

                            <option value="supplier">Supplier</option>

                            <option value="professional_service">
                                Professional Service
                            </option>

                            <option value="communication">Communication</option>

                            <option value="other">Other</option>
                        </select>

                        <select
                            className="rounded border p-2"
                            value={form.data.supplier_id}
                            onChange={(e) =>
                                form.setData('supplier_id', e.target.value)
                            }
                        >
                            <option value="">No supplier</option>

                            {suppliers.map((supplier: any) => (
                                <option key={supplier.id} value={supplier.id}>
                                    {supplier.name}
                                </option>
                            ))}
                        </select>

                        <input
                            type="date"
                            className="rounded border p-2"
                            value={form.data.expense_date}
                            onChange={(e) =>
                                form.setData('expense_date', e.target.value)
                            }
                        />

                        <input
                            className="rounded border p-2"
                            placeholder="Reference number"
                            value={form.data.reference_number}
                            onChange={(e) =>
                                form.setData('reference_number', e.target.value)
                            }
                        />

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
                            step="0.01"
                            className="rounded border p-2"
                            placeholder="Subtotal"
                            value={form.data.subtotal}
                            onChange={(e) =>
                                form.setData(
                                    'subtotal',
                                    e.target.value
                                        ? Number(e.target.value)
                                        : '',
                                )
                            }
                        />

                        <input
                            type="number"
                            min="0"
                            step="0.01"
                            className="rounded border p-2"
                            placeholder="Tax"
                            value={form.data.tax_amount}
                            onChange={(e) =>
                                form.setData(
                                    'tax_amount',
                                    e.target.value ? Number(e.target.value) : 0,
                                )
                            }
                        />

                        <select
                            className="rounded border p-2"
                            value={form.data.incurred_by}
                            onChange={(e) =>
                                form.setData('incurred_by', e.target.value)
                            }
                        >
                            <option value="">Current user</option>

                            {users.map((user: any) => (
                                <option key={user.id} value={user.id}>
                                    {user.name}
                                </option>
                            ))}
                        </select>

                        <textarea
                            className="min-h-24 rounded border p-2 md:col-span-3"
                            placeholder="Description"
                            value={form.data.description}
                            onChange={(e) =>
                                form.setData('description', e.target.value)
                            }
                        />

                        <input
                            type="file"
                            multiple
                            onChange={(e) =>
                                form.setData(
                                    'attachments',
                                    Array.from(e.target.files ?? []),
                                )
                            }
                        />

                        <label className="flex items-center gap-2">
                            <input
                                type="checkbox"
                                checked={form.data.billable_to_client}
                                onChange={(e) =>
                                    form.setData(
                                        'billable_to_client',
                                        e.target.checked,
                                    )
                                }
                            />
                            Billable to client
                        </label>

                        <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                            Create Expense
                        </button>
                    </form>
                )}

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Expense</th>
                                <th className="p-3 text-left">Date</th>
                                <th className="p-3 text-left">Title</th>
                                <th className="p-3 text-left">Supplier</th>
                                <th className="p-3 text-left">Total</th>
                                <th className="p-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            {expenses.data.map((expense: any) => (
                                <tr
                                    key={expense.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        <Link
                                            href={`/admin/expenses/${expense.public_id}`}
                                            className="font-medium underline"
                                        >
                                            {expense.expense_number}
                                        </Link>
                                    </td>

                                    <td className="p-3">
                                        {expense.expense_date}
                                    </td>

                                    <td className="p-3">{expense.title}</td>

                                    <td className="p-3">
                                        {expense.supplier?.name ?? '-'}
                                    </td>

                                    <td className="p-3">
                                        {expense.currency}{' '}
                                        {expense.total_amount}
                                    </td>

                                    <td className="p-3">{expense.status}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
