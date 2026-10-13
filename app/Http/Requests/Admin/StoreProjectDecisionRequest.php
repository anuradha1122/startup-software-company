<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'projects.decisions.manage'
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

            'decision' => [
                'required',
                'string',
                'max:20000',
            ],

            'rationale' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'decided_at' => [
                'required',
                'date',
            ],

            'client_visible' => [
                'boolean',
            ],
        ];
    }
}
