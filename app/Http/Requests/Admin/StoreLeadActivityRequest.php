<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('leads.update')
            ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in([
                    'call',
                    'email',
                    'meeting',
                    'note',
                    'follow_up',
                ]),
            ],

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'due_at' => [
                'nullable',
                'date',
            ],

            'completed' => [
                'boolean',
            ],
        ];
    }
}
