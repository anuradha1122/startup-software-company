<?php

namespace App\Services\Hr;

use App\Models\EmployeeProfile;
use App\Models\User;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmployeeService
{
    public function __construct(
        private readonly DocumentNumberService $numbers
    ) {}

    public function create(
        array $data,
        User $actor
    ): EmployeeProfile {
        if (
            EmployeeProfile::query()
                ->where(
                    'user_id',
                    $data['user_id']
                )
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'user_id' => 'This user already has an employee profile.',
            ]);
        }

        return EmployeeProfile::create([
            ...$data,

            'employee_number' => $this->numbers->next(
                'employee'
            ),

            'created_by' => $actor->id,

            'updated_by' => $actor->id,
        ]);
    }

    public function terminate(
        EmployeeProfile $employee,
        string $date,
        User $actor
    ): EmployeeProfile {
        return DB::transaction(
            function () use (
                $employee,
                $date,
                $actor
            ): EmployeeProfile {
                $locked =
                    EmployeeProfile::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $employee->id
                        );

                if (
                    $locked->status
                    === 'terminated'
                ) {
                    throw ValidationException::withMessages([
                        'status' => 'Employee is already terminated.',
                    ]);
                }

                $locked->update([
                    'status' => 'terminated',

                    'termination_date' => $date,

                    'updated_by' => $actor->id,
                ]);

                return $locked->refresh();
            }
        );
    }
}
