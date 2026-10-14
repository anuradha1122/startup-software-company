<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'payments.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'client_id' => [
                'required',
                'exists:clients,id',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'method' => [
                'required',
                Rule::in([
                    'bank_transfer',
                    'cash',
                    'card',
                    'cheque',
                    'online',
                    'other',
                ]),
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:150',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
