<?php

namespace App\Exports;

use App\Models\EmployeeProfile;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return EmployeeProfile::query()
            ->with('user')
            ->orderBy(
                'employee_number'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Employee Number',
            'Name',
            'Email',
            'Employment Type',
            'Status',
            'Department',
            'Job Title',
            'Hire Date',
            'Probation End Date',
            'Termination Date',
            'Work Location',
        ];
    }

    public function map(
        mixed $employee
    ): array {
        return [
            $employee->employee_number,

            $employee
                ->user
                ?->name,

            $employee
                ->user
                ?->email,

            $employee->employment_type,

            $employee->status,

            $employee->department,

            $employee->job_title,

            $employee
                ->hire_date
                ?->format(
                    'Y-m-d'
                ),

            $employee
                ->probation_end_date
                ?->format(
                    'Y-m-d'
                ),

            $employee
                ->termination_date
                ?->format(
                    'Y-m-d'
                ),

            $employee->work_location,
        ];
    }
}
