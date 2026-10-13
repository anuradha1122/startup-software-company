<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SyncResourceSkillsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'people.skills.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'skills' => [
                'array',
            ],

            'skills.*.skill_id' => [
                'required',
                'integer',
                'exists:skills,id',
            ],

            'skills.*.proficiency' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'skills.*.years_experience' => [
                'nullable',
                'integer',
                'min:0',
                'max:80',
            ],
        ];
    }
}
