<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'invoices.update'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'description' => [
                'required',
                'string',
                'max:1000',
            ],

            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tax_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
        ];
    }
}
