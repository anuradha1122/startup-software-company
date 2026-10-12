<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('leads.create')
            ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'stage_id' => [
                'required',
                Rule::exists('lead_stages', 'id')
                    ->where('active', true),
            ],

            'source_id' => [
                'nullable',
                Rule::exists('lead_sources', 'id')
                    ->where('active', true),
            ],

            'owner_id' => [
                'nullable',
                'exists:users,id',
            ],

            'estimated_value' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
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

            'next_follow_up_at' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'contact.name' => [
                'required',
                'string',
                'max:255',
            ],

            'contact.designation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'contact.email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'contact.phone' => [
                'nullable',
                'string',
                'max:30',
            ],
        ];
    }
}
