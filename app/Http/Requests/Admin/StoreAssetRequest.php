<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'assets.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'asset_tag' => [
                'required',
                'string',
                'max:100',
                'unique:assets,asset_tag',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'brand' => [
                'nullable',
                'string',
                'max:100',
            ],

            'model' => [
                'nullable',
                'string',
                'max:100',
            ],

            'serial_number' => [
                'nullable',
                'string',
                'max:255',
                'unique:assets,serial_number',
            ],

            'supplier_id' => [
                'nullable',
                'integer',
                'exists:suppliers,id',
            ],

            'purchase_order_item_id' => [
                'nullable',
                'integer',
                'exists:purchase_order_items,id',
            ],

            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],

            'currency' => [
                'nullable',
                'string',
                'size:3',
            ],

            'purchase_cost' => [
                'nullable',
                'decimal:0,2',
                'min:0',
            ],

            'purchase_date' => [
                'nullable',
                'date',
            ],

            'warranty_expires_at' => [
                'nullable',
                'date',
                'after_or_equal:purchase_date',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
