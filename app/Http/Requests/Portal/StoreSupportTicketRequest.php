<?php

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth(
            'client_portal'
        )->check();
    }

    public function rules(): array
    {
        return [
            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
                'max:20000',
            ],

            'priority' => [
                'required',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                    'urgent',
                ]),
            ],

            'severity' => [
                'required',
                Rule::in([
                    'minor',
                    'normal',
                    'major',
                    'critical',
                ]),
            ],
        ];
    }
}
