<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'client-approvals.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in([
                    'requirements',
                    'proposal',
                    'quotation',
                    'contract',
                    'milestone',
                    'deliverable',
                    'document',
                    'other',
                ]),
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'summary' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'source_type' => [
                'nullable',
                'string',
                'max:100',
            ],

            'source_id' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'due_at' => [
                'nullable',
                'date',
                'after:now',
            ],
        ];
    }
}
