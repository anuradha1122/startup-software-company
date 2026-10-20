<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AssignAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'assets.assign'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],

            'assigned_date' => [
                'required',
                'date',
            ],

            'expected_return_date' => [
                'nullable',
                'date',
                'after_or_equal:assigned_date',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'assignment_notes' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
