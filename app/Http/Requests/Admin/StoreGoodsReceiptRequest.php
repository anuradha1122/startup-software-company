<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoodsReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(
            'goods-receipts.create'
        ) ?? false;
    }

    public function rules(): array
    {
        return [
            'received_date' => [
                'required',
                'date',
            ],

            'delivery_reference' => [
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

            'items.*.purchase_order_item_id' => [
                'required',
                'integer',
                'exists:purchase_order_items,id',
                'distinct',
            ],

            'items.*.quantity_received' => [
                'required',
                'decimal:0,2',
                'min:0',
            ],

            'items.*.quantity_rejected' => [
                'nullable',
                'decimal:0,2',
                'min:0',
            ],

            'items.*.condition_notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ];
    }
}
