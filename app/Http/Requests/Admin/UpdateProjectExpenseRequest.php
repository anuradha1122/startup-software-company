<?php

namespace App\Http\Requests\Admin;

class UpdateProjectExpenseRequest extends StoreProjectExpenseRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'expenses.update'
        ) ?? false;
    }
}
