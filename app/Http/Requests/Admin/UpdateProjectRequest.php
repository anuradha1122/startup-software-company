<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'projects.update'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'health' => [
                'required',
                Rule::in([
                    'green',
                    'amber',
                    'red',
                ]),
            ],

            'methodology' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'project_manager_id' => [
                'required',
                'exists:users,id',
            ],

            'client_sponsor_contact_id' => [
                'nullable',
                'exists:client_contacts,id',
            ],

            'planned_start_date' => [
                'nullable',
                'date',
            ],

            'planned_end_date' => [
                'nullable',
                'date',
                'after_or_equal:planned_start_date',
            ],
        ];
    }
}
