<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContractDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'contracts.documents.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in([
                    'signed_contract',
                    'addendum',
                    'supporting_document',
                ]),
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'file' => [
                'required',
                'file',
                'max:15360',
                'mimes:pdf,png,jpg,jpeg,doc,docx',
            ],
        ];
    }
}
