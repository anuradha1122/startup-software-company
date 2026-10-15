<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientPortalUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'client-portal-users.update'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                Rule::in([
                    'invited',
                    'active',
                    'suspended',
                ]),
            ],
        ];
    }
}
