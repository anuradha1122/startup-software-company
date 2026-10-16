import { Head, useForm } from '@inertiajs/react';

type Compensation = {
    currency: string;
    monthly_basic_salary: string;
    fixed_allowance: string;
    fixed_deduction: string;
    effective_from: string;
};

type LeaveBalance = {
    year: number;
    available: string;
    leave_type: {
        name: string;
    };
};

type Employee = {
    public_id: string;
    employee_number: string;
    employment_type: string;
    status: string;
    department: string | null;
    job_title: string | null;
    hire_date: string;
    work_location: string | null;
    user: {
        public_id: string;
        name: string;
        email: string;
    };
    leave_balances: LeaveBalance[];
    compensation?: Compensation | null;
};

type Props = {
    employee: Employee;
    canViewCompensation: boolean;
    canManageCompensation: boolean;
};

type CompensationForm = {
    currency: string;
    monthly_basic_salary: string;
    fixed_allowance: string;
    fixed_deduction: string;
    effective_from: string;
    notes: string;
};

export default function EmployeeShow({
    employee,
    canViewCompensation,
    canManageCompensation,
}: Props) {
    const compensationForm = useForm<CompensationForm>({
        currency: employee.compensation?.currency ?? 'LKR',

        monthly_basic_salary: employee.compensation?.monthly_basic_salary ?? '',

        fixed_allowance: employee.compensation?.fixed_allowance ?? '0.00',

        fixed_deduction: employee.compensation?.fixed_deduction ?? '0.00',

        effective_from: employee.compensation?.effective_from ?? '',

        notes: '',
    });

    return (
        <>
            <Head title={employee.employee_number} />

            <div className="space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">
                        {employee.employee_number}
                    </h1>

                    <p className="text-sm text-muted-foreground">
                        {employee.user.name}
                    </p>
                </div>

                <section className="grid gap-4 rounded border p-5 md:grid-cols-2">
                    <Info label="Name" value={employee.user.name} />

                    <Info label="Email" value={employee.user.email} />

                    <Info
                        label="Employment Type"
                        value={employee.employment_type}
                    />

                    <Info label="Status" value={employee.status} />

                    <Info
                        label="Department"
                        value={employee.department ?? '-'}
                    />

                    <Info label="Job Title" value={employee.job_title ?? '-'} />

                    <Info label="Hire Date" value={employee.hire_date} />

                    <Info
                        label="Work Location"
                        value={employee.work_location ?? '-'}
                    />
                </section>

                <section className="rounded border p-5">
                    <h2 className="mb-4 text-lg font-semibold">
                        Leave Balances
                    </h2>

                    <div className="grid gap-3 md:grid-cols-3">
                        {employee.leave_balances.map((balance, index) => (
                            <div
                                key={`${balance.leave_type.name}-${balance.year}-${index}`}
                                className="rounded border p-4"
                            >
                                <p className="text-sm text-muted-foreground">
                                    {balance.leave_type.name}
                                </p>

                                <p className="text-xl font-semibold">
                                    {balance.available}
                                </p>

                                <p className="text-xs text-muted-foreground">
                                    {balance.year}
                                </p>
                            </div>
                        ))}
                    </div>
                </section>

                {canViewCompensation && (
                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">
                            Compensation
                        </h2>

                        {employee.compensation ? (
                            <div className="grid gap-4 md:grid-cols-2">
                                <Info
                                    label="Currency"
                                    value={employee.compensation.currency}
                                />

                                <Info
                                    label="Basic Salary"
                                    value={
                                        employee.compensation
                                            .monthly_basic_salary
                                    }
                                />

                                <Info
                                    label="Fixed Allowance"
                                    value={
                                        employee.compensation.fixed_allowance
                                    }
                                />

                                <Info
                                    label="Fixed Deduction"
                                    value={
                                        employee.compensation.fixed_deduction
                                    }
                                />

                                <Info
                                    label="Effective From"
                                    value={employee.compensation.effective_from}
                                />
                            </div>
                        ) : (
                            <p className="text-sm text-muted-foreground">
                                No active compensation record.
                            </p>
                        )}
                    </section>
                )}

                {canManageCompensation && (
                    <section className="rounded border p-5">
                        <h2 className="mb-4 text-lg font-semibold">
                            Update Compensation
                        </h2>

                        <form
                            className="grid gap-3 md:grid-cols-2"
                            onSubmit={(event) => {
                                event.preventDefault();

                                compensationForm.post(
                                    `/admin/hr/employees/${employee.public_id}/compensation`,
                                    {
                                        preserveScroll: true,
                                    },
                                );
                            }}
                        >
                            <input
                                value={compensationForm.data.currency}
                                onChange={(event) =>
                                    compensationForm.setData(
                                        'currency',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                                placeholder="Currency"
                            />

                            <input
                                value={
                                    compensationForm.data.monthly_basic_salary
                                }
                                onChange={(event) =>
                                    compensationForm.setData(
                                        'monthly_basic_salary',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                                placeholder="Basic salary"
                            />

                            <input
                                value={compensationForm.data.fixed_allowance}
                                onChange={(event) =>
                                    compensationForm.setData(
                                        'fixed_allowance',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                                placeholder="Fixed allowance"
                            />

                            <input
                                value={compensationForm.data.fixed_deduction}
                                onChange={(event) =>
                                    compensationForm.setData(
                                        'fixed_deduction',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                                placeholder="Fixed deduction"
                            />

                            <input
                                type="date"
                                value={compensationForm.data.effective_from}
                                onChange={(event) =>
                                    compensationForm.setData(
                                        'effective_from',
                                        event.target.value,
                                    )
                                }
                                className="rounded border p-2"
                            />

                            <textarea
                                value={compensationForm.data.notes}
                                onChange={(event) =>
                                    compensationForm.setData(
                                        'notes',
                                        event.target.value,
                                    )
                                }
                                className="min-h-24 rounded border p-2 md:col-span-2"
                                placeholder="Notes"
                            />

                            <button
                                className="rounded bg-primary px-4 py-2 text-primary-foreground"
                                disabled={compensationForm.processing}
                            >
                                Save Compensation
                            </button>
                        </form>
                    </section>
                )}
            </div>
        </>
    );
}

function Info({ label, value }: { label: string; value: string }) {
    return (
        <div>
            <p className="text-sm text-muted-foreground">{label}</p>

            <p>{value}</p>
        </div>
    );
}
