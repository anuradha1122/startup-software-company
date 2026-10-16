import { Head, router } from '@inertiajs/react';

type AttendanceRecord = {
    public_id: string;
    work_date: string;
    clock_in_at: string | null;
    clock_out_at: string | null;
    worked_hours: string;
    overtime_hours: string;
    status: string;
};

type Props = {
    today: AttendanceRecord | null;
    records: AttendanceRecord[];
};

export default function EmployeeAttendance({ today, records }: Props) {
    const canClockIn = !today?.clock_in_at;

    const canClockOut = Boolean(today?.clock_in_at) && !today?.clock_out_at;

    return (
        <>
            <Head title="My Attendance" />

            <div className="space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">My Attendance</h1>
                </div>

                <section className="rounded border p-5">
                    <h2 className="font-semibold">Today</h2>

                    <div className="mt-4 flex flex-wrap gap-3">
                        {canClockIn && (
                            <button
                                type="button"
                                onClick={() =>
                                    router.post('/employee/attendance/clock-in')
                                }
                                className="rounded bg-primary px-4 py-2 text-primary-foreground"
                            >
                                Clock In
                            </button>
                        )}

                        {canClockOut && (
                            <button
                                type="button"
                                onClick={() =>
                                    router.post(
                                        '/employee/attendance/clock-out',
                                    )
                                }
                                className="rounded border px-4 py-2"
                            >
                                Clock Out
                            </button>
                        )}

                        {today && (
                            <div className="text-sm">
                                <p>In: {today.clock_in_at ?? '-'}</p>
                                <p>Out: {today.clock_out_at ?? '-'}</p>
                            </div>
                        )}
                    </div>
                </section>

                <section className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Date</th>
                                <th className="p-3 text-left">In</th>
                                <th className="p-3 text-left">Out</th>
                                <th className="p-3 text-left">Hours</th>
                                <th className="p-3 text-left">Overtime</th>
                                <th className="p-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            {records.map((record) => (
                                <tr key={record.public_id} className="border-t">
                                    <td className="p-3">{record.work_date}</td>

                                    <td className="p-3">
                                        {record.clock_in_at ?? '-'}
                                    </td>

                                    <td className="p-3">
                                        {record.clock_out_at ?? '-'}
                                    </td>

                                    <td className="p-3">
                                        {record.worked_hours}
                                    </td>

                                    <td className="p-3">
                                        {record.overtime_hours}
                                    </td>

                                    <td className="p-3">{record.status}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </section>
            </div>
        </>
    );
}
