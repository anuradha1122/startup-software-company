<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectCharterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'projects.charter.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'business_objective' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'project_objective' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'governance_structure' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'communication_plan' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'escalation_process' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'success_criteria' => [
                'array',
            ],

            'success_criteria.*' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'assumptions' => [
                'array',
            ],

            'assumptions.*' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'constraints' => [
                'array',
            ],

            'constraints.*' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
