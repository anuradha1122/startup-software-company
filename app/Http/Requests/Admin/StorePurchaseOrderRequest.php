<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'purchase-orders.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'procurement_request_id' => [
                'required',
                'string',
                'exists:procurement_requests,public_id',
            ],

            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],

            'currency' => [
                'required',
                'string',
                'size:3',
            ],

            'order_date' => [
                'required',
                'date',
            ],

            'expected_delivery_date' => [
                'nullable',
                'date',
                'after_or_equal:order_date',
            ],

            'tax_amount' => [
                'nullable',
                'decimal:0,2',
                'min:0',
            ],

            'supplier_reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.procurement_request_item_id' => [
                'required',
                'integer',
                'exists:procurement_request_items,id',
                'distinct',
            ],

            'items.*.unit_cost' => [
                'required',
                'decimal:0,2',
                'min:0',
            ],

            'items.*.asset_trackable' => [
                'required',
                'boolean',
            ],
        ];
    }
}
