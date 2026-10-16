<?php

namespace App\Exports;

use App\Models\PayrollEntry;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PayrollRegisterExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return PayrollEntry::query()
            ->with([
                'employee.user',
                'period',
            ])
            ->latest('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Payroll Period',
            'Employee Number',
            'Employee',
            'Currency',
            'Basic Salary',
            'Allowances',
            'Overtime',
            'Gross Pay',
            'Deductions',
            'Net Pay',
            'Attendance Days',
            'Leave Days',
            'Unpaid Leave Days',
            'Calculated At',
        ];
    }

    public function map(
        mixed $entry
    ): array {
        return [
            $entry
                ->period
                ?->name,

            $entry
                ->employee
                ?->employee_number,

            $entry
                ->employee
                ?->user
                ?->name,

            $entry->currency,

            $entry->basic_salary,

            $entry->allowances,

            $entry->overtime_amount,

            $entry->gross_pay,

            $entry->deductions,

            $entry->net_pay,

            $entry->attendance_days,

            $entry->leave_days,

            $entry->unpaid_leave_days,

            $entry
                ->calculated_at
                ?->format(
                    'Y-m-d H:i:s'
                ),
        ];
    }
}
