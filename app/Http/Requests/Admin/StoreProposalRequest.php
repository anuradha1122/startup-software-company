<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'proposals.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'requirement_version_id' => [
                'required',
                'exists:requirement_versions,id',
            ],

            'estimate_id' => [
                'required',
                'exists:estimates,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'executive_summary' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'solution_overview' => [
                'nullable',
                'string',
                'max:30000',
            ],

            'delivery_approach' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'commercial_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'valid_until' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
        ];
    }
}
