<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MoveProjectTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'tasks.move'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'workflow_status_id' => [
                'required',
                'exists:task_workflow_statuses,id',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
