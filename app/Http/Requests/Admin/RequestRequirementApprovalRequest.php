<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RequestRequirementApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'requirements.request-approval'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'client_contact_id' => [
                'required',
                'exists:client_contacts,id',
            ],
        ];
    }
}
