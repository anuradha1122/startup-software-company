<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRiskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'projects.risks.manage'
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

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'likelihood' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'impact' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'open',
                    'mitigating',
                    'accepted',
                    'closed',
                ]),
            ],

            'owner_id' => [
                'nullable',
                'exists:users,id',
            ],

            'mitigation' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'contingency' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'review_date' => [
                'nullable',
                'date',
            ],
        ];
    }
}
