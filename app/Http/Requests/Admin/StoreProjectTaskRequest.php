<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'tasks.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'parent_task_id' => [
                'nullable',
                'exists:project_tasks,id',
            ],

            'project_phase_id' => [
                'nullable',
                'exists:project_phases,id',
            ],

            'project_milestone_id' => [
                'nullable',
                'exists:project_milestones,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:30000',
            ],

            'type' => [
                'required',
                Rule::in([
                    'task',
                    'bug',
                    'change',
                    'research',
                ]),
            ],

            'priority' => [
                'required',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                    'urgent',
                ]),
            ],

            'assignee_id' => [
                'nullable',
                'exists:users,id',
            ],

            'estimated_hours' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'planned_start_date' => [
                'nullable',
                'date',
            ],

            'due_date' => [
                'nullable',
                'date',
                'after_or_equal:planned_start_date',
            ],

            'client_visible' => [
                'boolean',
            ],

            'dependency_ids' => [
                'array',
            ],

            'dependency_ids.*' => [
                'integer',
                'exists:project_tasks,id',
            ],
        ];
    }
}
