<?php

namespace App\Http\Requests\Admin;

class UpdateProjectTaskRequest extends StoreProjectTaskRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'tasks.update'
        ) ?? false;
    }
}
