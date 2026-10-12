<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequirementAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'requirements.attachments.manage'
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
                'mimes:pdf,png,jpg,jpeg,doc,docx,xls,xlsx,txt',
            ],
        ];
    }
}
