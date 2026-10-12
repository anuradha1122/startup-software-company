<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecordCommercialDecisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision' => [
                'required',
                Rule::in([
                    'accepted',
                    'rejected',
                ]),
            ],

            'decision_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
