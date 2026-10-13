<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResourceProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'people.update'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'job_title' => [
                'nullable',
                'string',
                'max:150',
            ],

            'department' => [
                'nullable',
                'string',
                'max:150',
            ],

            'capacity_hours_per_week' => [
                'required',
                'integer',
                'min:1',
                'max:168',
            ],

            'default_allocation_percent' => [
                'required',
                'integer',
                'between:1,100',
            ],

            'seniority' => [
                'nullable',
                Rule::in([
                    'trainee',
                    'junior',
                    'mid',
                    'senior',
                    'lead',
                    'principal',
                ]),
            ],

            'bio' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'available_for_projects' => [
                'boolean',
            ],
        ];
    }
}
