<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimesheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'timesheets.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'week_start' => [
                'required',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
