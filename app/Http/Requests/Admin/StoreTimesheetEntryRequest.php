<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimesheetEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'timesheets.update'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'project_id' => [
                'required',
                'exists:projects,id',
            ],

            'project_task_id' => [
                'required',
                'exists:project_tasks,id',
            ],

            'work_date' => [
                'required',
                'date',
            ],

            'hours' => [
                'required',
                'numeric',
                'gt:0',
                'lte:24',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'billable' => [
                'boolean',
            ],
        ];
    }
}
