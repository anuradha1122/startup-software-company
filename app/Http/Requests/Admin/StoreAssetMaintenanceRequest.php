<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'assets.maintain'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in([
                    'inspection',
                    'repair',
                    'service',
                    'upgrade',
                ]),
            ],

            'started_date' => [
                'nullable',
                'date',
            ],

            'vendor' => [
                'nullable',
                'string',
                'max:255',
            ],

            'currency' => [
                'nullable',
                'string',
                'size:3',
            ],

            'cost' => [
                'nullable',
                'decimal:0,2',
                'min:0',
            ],

            'description' => [
                'required',
                'string',
                'max:10000',
            ],
        ];
    }
}
