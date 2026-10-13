<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'projects.milestones.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'project_phase_id' => [
                'nullable',
                'exists:project_phases,id',
            ],

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

            'due_date' => [
                'required',
                'date',
            ],

            'client_visible' => [
                'boolean',
            ],
        ];
    }
}
