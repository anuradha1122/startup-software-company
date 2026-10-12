<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordRequirementApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'requirements.record-approval'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'decision' => [
                'required',
                Rule::in([
                    'approved',
                    'revision_required',
                ]),
            ],

            'decision_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
