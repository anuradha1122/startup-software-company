<?php

namespace App\Exports;

use App\Models\AttendanceRecord;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return AttendanceRecord::query()
            ->with(
                'employee.user'
            )
            ->orderByDesc(
                'work_date'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Employee Number',
            'Employee',
            'Date',
            'Clock In',
            'Clock Out',
            'Worked Hours',
            'Overtime Hours',
            'Status',
            'Source',
            'Notes',
        ];
    }

    public function map(
        mixed $attendance
    ): array {
        return [
            $attendance
                ->employee
                ?->employee_number,

            $attendance
                ->employee
                ?->user
                ?->name,

            $attendance
                ->work_date
                ?->format(
                    'Y-m-d'
                ),

            $attendance
                ->clock_in_at
                ?->format(
                    'Y-m-d H:i:s'
                ),

            $attendance
                ->clock_out_at
                ?->format(
                    'Y-m-d H:i:s'
                ),

            $attendance
                ->worked_hours,

            $attendance
                ->overtime_hours,

            $attendance
                ->status,

            $attendance
                ->source,

            $attendance
                ->notes,
        ];
    }
}
