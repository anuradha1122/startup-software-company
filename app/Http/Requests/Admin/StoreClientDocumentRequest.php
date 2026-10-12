<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'clients.documents.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'category' => [
                'required',
                Rule::in([
                    'registration',
                    'tax',
                    'agreement',
                    'billing',
                    'identification',
                    'other',
                ]),
            ],

            'visibility' => [
                'required',
                Rule::in([
                    'internal',
                    'client',
                ]),
            ],

            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,png,jpg,jpeg,doc,docx,xls,xlsx',
            ],
        ];
    }
}
