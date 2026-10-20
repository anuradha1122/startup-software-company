<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CompleteAssetMaintenanceRequest extends FormRequest
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
            'completed_date' => [
                'required',
                'date',
            ],

            'resolution' => [
                'required',
                'string',
                'max:10000',
            ],
        ];
    }
}
