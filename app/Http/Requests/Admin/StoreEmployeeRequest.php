<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'employees.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                'unique:employee_profiles,user_id',
            ],

            'employment_type' => [
                'required',
                Rule::in([
                    'permanent',
                    'probation',
                    'contract',
                    'intern',
                    'part_time',
                ]),
            ],

            'department' => [
                'nullable',
                'string',
                'max:150',
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:150',
            ],

            'hire_date' => [
                'required',
                'date',
            ],

            'probation_end_date' => [
                'nullable',
                'date',
                'after_or_equal:hire_date',
            ],

            'work_location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:30',
            ],
        ];
    }
}
