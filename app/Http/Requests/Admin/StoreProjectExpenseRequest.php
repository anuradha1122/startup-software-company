<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'expenses.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => [
                'nullable',
                'exists:suppliers,id',
            ],

            'type' => [
                'required',
                Rule::in([
                    'travel',
                    'software',
                    'hosting',
                    'equipment',
                    'supplier',
                    'professional_service',
                    'communication',
                    'other',
                ]),
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'expense_date' => [
                'required',
                'date',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:150',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'subtotal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tax_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'billable_to_client' => [
                'boolean',
            ],

            'incurred_by' => [
                'nullable',
                'exists:users,id',
            ],

            'attachments' => [
                'array',
                'max:10',
            ],

            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:pdf,png,jpg,jpeg,doc,docx,xls,xlsx,txt',
            ],
        ];
    }
}
