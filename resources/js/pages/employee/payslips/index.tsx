import { Head } from '@inertiajs/react';

type Payslip = {
    public_id: string;
    currency: string;
    gross_pay: string;
    deductions: string;
    net_pay: string;
    period: {
        name: string;
        payment_date: string;
    };
};

type Props = {
    payslips: Payslip[];
};

export default function Payslips({ payslips }: Props) {
    return (
        <>
            <Head title="My Payslips" />

            <div className="space-y-6 p-6">
                <div>
                    <h1 className="text-2xl font-semibold">My Payslips</h1>

                    <p className="text-sm text-muted-foreground">
                        Published payroll records only.
                    </p>
                </div>

                <div className="overflow-x-auto rounded border">
                    <table className="w-full text-sm">
                        <thead className="bg-muted">
                            <tr>
                                <th className="p-3 text-left">Period</th>
                                <th className="p-3 text-right">Gross</th>
                                <th className="p-3 text-right">Deductions</th>
                                <th className="p-3 text-right">Net</th>
                                <th className="p-3 text-left">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            {payslips.map((payslip) => (
                                <tr
                                    key={payslip.public_id}
                                    className="border-t"
                                >
                                    <td className="p-3">
                                        {payslip.period.name}
                                    </td>

                                    <td className="p-3 text-right">
                                        {payslip.currency} {payslip.gross_pay}
                                    </td>

                                    <td className="p-3 text-right">
                                        {payslip.currency} {payslip.deductions}
                                    </td>

                                    <td className="p-3 text-right font-medium">
                                        {payslip.currency} {payslip.net_pay}
                                    </td>

                                    <td className="p-3">
                                        <a
                                            href={`/employee/payslips/${payslip.public_id}/pdf`}
                                            className="underline"
                                        >
                                            PDF
                                        </a>
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
