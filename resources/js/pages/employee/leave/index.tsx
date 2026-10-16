import { Head, useForm } from '@inertiajs/react';

type LeaveType = {
    id: number;
    public_id: string;
    code: string;
    name: string;
    paid: boolean;
};

type Balance = {
    id: number;
    year: number;
    available: string;
    leave_type: {
        name: string;
    };
};

type LeaveRequest = {
    public_id: string;
    leave_number: string;
    start_date: string;
    end_date: string;
    days: string;
    status: string;
    reason: string | null;
    leave_type: {
        name: string;
    };
};

type Props = {
    leaveTypes: LeaveType[];
    balances: Balance[];
    requests: LeaveRequest[];
};

type FormData = {
    leave_type_id: string;
    start_date: string;
    end_date: string;
    reason: string;
};

export default function EmployeeLeave({
    leaveTypes,
    balances,
    requests,
}: Props) {
    const form = useForm<FormData>({
        leave_type_id: '',
        start_date: '',
        end_date: '',
        reason: '',
    });

    return (
        <>
            <Head title="My Leave" />

            <div className="space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">My Leave</h1>

                    <p className="text-sm text-muted-foreground">
                        Leave balances and requests.
                    </p>
                </div>

                <section className="grid gap-4 md:grid-cols-3">
                    {balances.map((balance) => (
                        <div key={balance.id} className="rounded border p-4">
                            <p className="text-sm text-muted-foreground">
                                {balance.leave_type.name}
                            </p>

                            <p className="text-2xl font-semibold">
                                {balance.available}
                            </p>

                            <p className="text-xs text-muted-foreground">
                                days available
                            </p>
                        </div>
                    ))}
                </section>

                <form
                    className="grid gap-3 rounded border p-5 md:grid-cols-2"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/employee/leave', {
                            preserveScroll: true,
                            onSuccess: () => form.reset(),
                        });
                    }}
                >
                    <select
                        value={form.data.leave_type_id}
                        onChange={(event) =>
                            form.setData('leave_type_id', event.target.value)
                        }
                        className="rounded border p-2"
                    >
                        <option value="">Select leave type</option>

                        {leaveTypes.map((type) => (
                            <option key={type.id} value={type.id}>
                                {type.name}
                            </option>
                        ))}
                    </select>

                    <div />

                    <input
                        type="date"
                        value={form.data.start_date}
                        onChange={(event) =>
                            form.setData('start_date', event.target.value)
                        }
                        className="rounded border p-2"
                    />

                    <input
                        type="date"
                        value={form.data.end_date}
                        onChange={(event) =>
                            form.setData('end_date', event.target.value)
                        }
                        className="rounded border p-2"
                    />

                    <textarea
                        value={form.data.reason}
                        onChange={(event) =>
                            form.setData('reason', event.target.value)
                        }
                        placeholder="Reason"
                        className="min-h-24 rounded border p-2 md:col-span-2"
                    />

                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                        Submit Leave Request
                    </button>
                </form>

                <section className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Request</th>
                                <th className="p-3 text-left">Type</th>
                                <th className="p-3 text-left">Dates</th>
                                <th className="p-3 text-left">Days</th>
                                <th className="p-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            {requests.map((request) => (
                                <tr
                                    key={request.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        {request.leave_number}
                                    </td>

                                    <td className="p-3">
                                        {request.leave_type.name}
                                    </td>

                                    <td className="p-3">
                                        {request.start_date}
                                        {' → '}
                                        {request.end_date}
                                    </td>

                                    <td className="p-3">{request.days}</td>

                                    <td className="p-3">{request.status}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </section>
            </div>
        </>
    );
}
