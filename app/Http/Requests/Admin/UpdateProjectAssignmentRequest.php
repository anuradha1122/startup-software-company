<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'project-assignments.update'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'role' => [
                'required',
                'string',
                'max:150',
            ],

            'allocation_percent' => [
                'required',
                'integer',
                'between:1,100',
            ],

            'status' => [
                'required',
                Rule::in([
                    'planned',
                    'active',
                ]),
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'billable' => [
                'boolean',
            ],

            'responsibilities' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
