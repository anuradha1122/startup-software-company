<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RetireAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'assets.retire'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'reason' => [
                'required',
                'string',
                'max:10000',
            ],
        ];
    }
}
