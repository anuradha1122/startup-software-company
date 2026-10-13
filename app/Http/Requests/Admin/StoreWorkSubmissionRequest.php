<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'work-submissions.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'summary' => [
                'required',
                'string',
                'max:30000',
            ],

            'testing_notes' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'deployment_notes' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'reference_url' => [
                'nullable',
                'url',
                'max:1000',
            ],

            'attachments' => [
                'array',
                'max:10',
            ],

            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:pdf,png,jpg,jpeg,txt,zip,doc,docx,xls,xlsx',
            ],
        ];
    }
}
