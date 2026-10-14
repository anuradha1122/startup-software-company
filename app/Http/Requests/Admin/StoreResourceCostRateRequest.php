<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceCostRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'internal-costs.manage-rates'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'hourly_cost_rate' => [
                'required',
                'numeric',
                'min:0',
            ],

            'effective_from' => [
                'required',
                'date',
            ],

            'effective_to' => [
                'nullable',
                'date',
                'after_or_equal:effective_from',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
