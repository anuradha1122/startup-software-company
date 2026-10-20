<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProcurementRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'procurement-requests.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'business_reason' => [
                'required',
                'string',
                'max:10000',
            ],

            'priority' => [
                'required',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                    'urgent',
                ]),
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.description' => [
                'required',
                'string',
                'max:500',
            ],

            'items.*.quantity' => [
                'required',
                'decimal:0,2',
                'gt:0',
            ],

            'items.*.unit' => [
                'nullable',
                'string',
                'max:50',
            ],

            'items.*.estimated_unit_cost' => [
                'required',
                'decimal:0,2',
                'min:0',
            ],

            'items.*.notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
