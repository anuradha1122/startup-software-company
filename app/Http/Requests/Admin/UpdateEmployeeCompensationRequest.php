<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeCompensationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'employee-compensation.manage'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'monthly_basic_salary' => [
                'required',
                'decimal:0,2',
                'min:0',
            ],

            'fixed_allowance' => [
                'required',
                'decimal:0,2',
                'min:0',
            ],

            'fixed_deduction' => [
                'required',
                'decimal:0,2',
                'min:0',
            ],

            'effective_from' => [
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
