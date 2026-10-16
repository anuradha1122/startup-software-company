<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeaveRequestsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return LeaveRequest::query()
            ->with([
                'employee.user',
                'leaveType',
                'reviewer',
            ])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Leave Number',
            'Employee Number',
            'Employee',
            'Leave Type',
            'Start Date',
            'End Date',
            'Days',
            'Status',
            'Submitted At',
            'Reviewer',
            'Reviewed At',
        ];
    }

    public function map(
        mixed $leave
    ): array {
        return [
            $leave->leave_number,

            $leave
                ->employee
                ?->employee_number,

            $leave
                ->employee
                ?->user
                ?->name,

            $leave
                ->leaveType
                ?->name,

            $leave
                ->start_date
                ?->format('Y-m-d'),

            $leave
                ->end_date
                ?->format('Y-m-d'),

            $leave->days,

            $leave->status,

            $leave
                ->submitted_at
                ?->format(
                    'Y-m-d H:i:s'
                ),

            $leave
                ->reviewer
                ?->name,

            $leave
                ->reviewed_at
                ?->format(
                    'Y-m-d H:i:s'
                ),
        ];
    }
}
