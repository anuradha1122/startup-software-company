<?php

namespace App\Http\Requests\Portal;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketMessageRequest extends FormRequest
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
            'message' => [
                'required',
                'string',
                'max:20000',
            ],
        ];
    }
}
