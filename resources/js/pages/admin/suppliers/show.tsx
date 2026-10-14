import { Head } from '@inertiajs/react';

export default function SupplierShow({ supplier }: any) {
    return (
        <>
            <Head title={supplier.name} />

            <div className="max-w-6xl space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">
                        {supplier.supplier_number}
                        {' — '}
                        {supplier.name}
                    </h1>

                    <p className="text-sm text-muted-foreground">
                        {supplier.status}
                    </p>
                </div>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-2">
                    <div>
                        <span className="text-sm text-muted-foreground">
                            Email
                        </span>

                        <p>{supplier.email ?? '-'}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Phone
                        </span>

                        <p>{supplier.phone ?? '-'}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Currency
                        </span>

                        <p>{supplier.default_currency}</p>
                    </div>

                    <div>
                        <span className="text-sm text-muted-foreground">
                            Country
                        </span>

                        <p>{supplier.country ?? '-'}</p>
                    </div>
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Related Expenses
                    </h2>

                    {supplier.expenses.map((expense: any) => (
                        <div
                            key={expense.public_id}
                            className="mb-2 rounded border p-3"
                        >
                            <strong>{expense.expense_number}</strong>

                            {' — '}
                            {expense.title}

                            <p className="text-sm text-muted-foreground">
                                {expense.project.project_number}
                                {' · '}
                                {expense.currency} {expense.total_amount}
                            </p>
                        </div>
                    ))}
                </section>
            </div>
        </>
    );
}
