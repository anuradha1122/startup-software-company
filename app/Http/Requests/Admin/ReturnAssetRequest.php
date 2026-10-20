<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReturnAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'assets.return'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'returned_date' => [
                'required',
                'date',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'return_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
