<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'support-tickets.update'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'priority' => [
                'required',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                    'urgent',
                ]),
            ],

            'severity' => [
                'required',
                Rule::in([
                    'minor',
                    'normal',
                    'major',
                    'critical',
                ]),
            ],

            'assigned_to' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'status' => [
                'required',
                Rule::in([
                    'open',
                    'assigned',
                    'in_progress',
                    'waiting_client',
                    'resolved',
                    'closed',
                    'cancelled',
                ]),
            ],
        ];
    }
}
