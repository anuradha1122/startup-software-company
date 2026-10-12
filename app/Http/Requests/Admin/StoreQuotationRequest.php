<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'quotations.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'proposal_id' => [
                'nullable',
                'exists:proposals,id',
            ],

            'requirement_version_id' => [
                'required',
                'exists:requirement_versions,id',
            ],

            'estimate_id' => [
                'required',
                'exists:estimates,id',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'discount_percent' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'tax_percent' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
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

            'terms_and_conditions' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'valid_until' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
        ];
    }
}
