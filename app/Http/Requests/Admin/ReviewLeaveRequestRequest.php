<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewLeaveRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'leave-requests.review'
        ) ?? false;
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

            'review_notes' => [
                'nullable',
                'string',
                'max:10000',
                'required_if:decision,rejected',
            ],
        ];
    }
}
