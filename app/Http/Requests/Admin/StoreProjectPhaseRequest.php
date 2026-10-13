<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectPhaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'projects.phases.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'planned_start_date' => [
                'nullable',
                'date',
            ],

            'planned_end_date' => [
                'nullable',
                'date',
                'after_or_equal:planned_start_date',
            ],
        ];
    }
}
