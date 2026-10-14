import { Head, Link, useForm } from '@inertiajs/react';

type TimesheetForm = {
    week_start: string;
    notes: string;
};

export default function TimesheetIndex({ timesheets }: any) {
    const form = useForm<TimesheetForm>({
        week_start: '',
        notes: '',
    });

    return (
        <>
            <Head title="Timesheets" />

            <div className="space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">Timesheets</h1>

                    <p className="text-sm text-muted-foreground">
                        Weekly project time recording.
                    </p>
                </div>

                <form
                    className="flex flex-wrap gap-3 rounded border p-4"
                    onSubmit={(event) => {
                        event.preventDefault();

                        form.post('/admin/timesheets');
                    }}
                >
                    <input
                        type="date"
                        className="rounded border p-2"
                        value={form.data.week_start}
                        onChange={(e) =>
                            form.setData('week_start', e.target.value)
                        }
                    />

                    <button className="rounded bg-primary px-4 py-2 text-primary-foreground">
                        Open Week
                    </button>
                </form>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Person</th>

                                <th className="p-3 text-left">Week</th>

                                <th className="p-3 text-left">Status</th>

                                <th className="p-3 text-left">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            {timesheets.data.map((timesheet: any) => (
                                <tr
                                    key={timesheet.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        {timesheet.user.name}
                                    </td>

                                    <td className="p-3">
                                        {timesheet.week_start}
                                        {' → '}
                                        {timesheet.week_end}
                                    </td>

                                    <td className="p-3">{timesheet.status}</td>

                                    <td className="p-3">
                                        <Link
                                            href={`/admin/timesheets/${timesheet.public_id}`}
                                            className="underline"
                                        >
                                            Open
                                        </Link>
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
