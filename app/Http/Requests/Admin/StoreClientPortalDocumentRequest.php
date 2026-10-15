<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientPortalDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'client-portal-documents.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'category' => [
                'required',
                Rule::in([
                    'requirements',
                    'proposal',
                    'quotation',
                    'contract',
                    'project',
                    'deliverable',
                    'invoice_support',
                    'other',
                ]),
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after:now',
            ],

            'file' => [
                'required',
                'file',
                'max:20480',
                'mimes:pdf,png,jpg,jpeg,doc,docx,xls,xlsx,txt',
            ],
        ];
    }
}
