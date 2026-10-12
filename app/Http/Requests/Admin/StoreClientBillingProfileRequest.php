<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientBillingProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'clients.billing.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'billing_name' => [
                'required',
                'string',
                'max:255',
            ],

            'billing_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'billing_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'billing_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'country' => [
                'required',
                'string',
                'max:100',
            ],

            'tax_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'payment_terms_days' => [
                'required',
                'integer',
                'min:0',
                'max:365',
            ],

            'invoice_notes' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'is_default' => [
                'boolean',
            ],
        ];
    }
}
