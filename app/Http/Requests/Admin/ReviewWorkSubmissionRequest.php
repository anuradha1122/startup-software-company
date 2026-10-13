<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewWorkSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'work-submissions.review'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'decision' => [
                'required',
                Rule::in([
                    'approved',
                    'changes_required',
                ]),
            ],

            'review_notes' => [
                'nullable',
                'string',
                'max:20000',
            ],
        ];
    }
}
