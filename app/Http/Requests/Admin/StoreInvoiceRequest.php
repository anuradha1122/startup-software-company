<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'invoices.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'project_id' => [
                'required',
                'exists:projects,id',
            ],

            'billing_contact_id' => [
                'nullable',
                'exists:client_contacts,id',
            ],

            'invoice_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'required',
                'date',
                'after_or_equal:invoice_date',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

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

            'billing_address' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'payment_instructions' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
