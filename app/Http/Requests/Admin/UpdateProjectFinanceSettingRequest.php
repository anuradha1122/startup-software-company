<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectFinanceSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'project-finance-settings.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'target_margin_percent' => [
                'required',
                'numeric',
                'min:-1000',
                'max:100',
            ],

            'warning_margin_percent' => [
                'required',
                'numeric',
                'min:-1000',
                'max:100',
                'lte:target_margin_percent',
            ],

            'alerts_enabled' => [
                'required',
                'boolean',
            ],
        ];
    }
}
