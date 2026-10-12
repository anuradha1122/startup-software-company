<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'contracts.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'quotation_id' => [
                'required',
                'exists:quotations,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'payment_terms_days' => [
                'required',
                'integer',
                'min:0',
                'max:365',
            ],

            'payment_schedule' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'special_terms' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'termination_terms' => [
                'nullable',
                'string',
                'max:20000',
            ],
        ];
    }
}
