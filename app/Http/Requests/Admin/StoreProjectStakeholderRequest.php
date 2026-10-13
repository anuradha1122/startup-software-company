<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectStakeholderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'projects.stakeholders.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in([
                    'internal',
                    'client',
                ]),
            ],

            'user_id' => [
                'nullable',
                'exists:users,id',
            ],

            'client_contact_id' => [
                'nullable',
                'exists:client_contacts,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'role' => [
                'required',
                'string',
                'max:150',
            ],

            'responsibility' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'is_primary' => [
                'boolean',
            ],

            'receive_notifications' => [
                'boolean',
            ],
        ];
    }
}
