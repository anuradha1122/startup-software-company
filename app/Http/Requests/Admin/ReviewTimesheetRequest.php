<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewTimesheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'timesheets.review'
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
            ],
        ];
    }
}
