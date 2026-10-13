<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectTaskCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'tasks.comment'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'body' => [
                'required',
                'string',
                'max:20000',
            ],

            'client_visible' => [
                'boolean',
            ],
        ];
    }
}
