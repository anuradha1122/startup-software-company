<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientPortalUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'client-portal-users.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'client_contact_id' => [
                'nullable',
                'integer',
                'exists:client_contacts,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(
                    'client_portal_users',
                    'email'
                ),
            ],
        ];
    }
}
