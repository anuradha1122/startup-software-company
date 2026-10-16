<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreChangeRequestEstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'change-requests.estimate'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'client_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'internal_estimated_cost' => [
                'required',
                'numeric',
                'min:0',
            ],

            'estimated_hours' => [
                'required',
                'numeric',
                'min:0',
            ],

            'estimated_days' => [
                'required',
                'integer',
                'min:0',
            ],

            'client_notes' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'internal_notes' => [
                'nullable',
                'string',
                'max:20000',
            ],
        ];
    }
}
