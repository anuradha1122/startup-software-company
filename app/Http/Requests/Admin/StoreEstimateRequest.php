<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'estimates.update'
        ) || $this->user()?->can(
            'estimates.create'
        );
    }

    public function rules(): array
    {
        return [
            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'contingency_percent' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'assumptions' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'internal_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'lines' => [
                'required',
                'array',
                'min:1',
            ],

            'lines.*.module_name' => [
                'required',
                'string',
                'max:255',
            ],

            'lines.*.description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'lines.*.estimated_hours' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'lines.*.billing_rate' => [
                'required',
                'numeric',
                'min:0',
            ],

            'lines.*.cost_rate' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }
}
