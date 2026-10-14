<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentAllocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'payments.allocate'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => [
                'required',
                'exists:invoices,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ];
    }
}
