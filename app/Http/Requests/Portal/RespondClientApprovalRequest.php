<?php

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RespondClientApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth(
            'client_portal'
        )->check();
    }

    public function rules(): array
    {
        return [
            'decision' => [
                'required',
                Rule::in([
                    'approved',
                    'rejected',
                ]),
            ],

            'response_comment' => [
                'nullable',
                'string',
                'max:10000',
                'required_if:decision,rejected',
            ],
        ];
    }
}
