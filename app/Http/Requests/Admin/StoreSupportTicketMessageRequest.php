<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportTicketMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'support-tickets.reply'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'message' => [
                'required',
                'string',
                'max:20000',
            ],

            'internal_only' => [
                'required',
                'boolean',
            ],
        ];
    }
}
