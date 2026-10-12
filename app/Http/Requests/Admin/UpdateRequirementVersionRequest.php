<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequirementVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'requirements.update'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'version_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'objective' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'scope_in' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'scope_out' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'functional_requirements' => [
                'array',
            ],

            'functional_requirements.*' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'non_functional_requirements' => [
                'array',
            ],

            'non_functional_requirements.*' => [
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

            'acceptance_criteria' => [
                'array',
            ],

            'acceptance_criteria.*' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'client_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'internal_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
