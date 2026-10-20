<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GoodsReceiptItem extends Model
{
    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_item_id',
        'quantity_received',
        'quantity_rejected',
        'condition_notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (GoodsReceiptItem $item): void {
            $item->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'quantity_received' => 'decimal:2',
            'quantity_rejected' => 'decimal:2',
        ];
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(
            GoodsReceipt::class,
            'goods_receipt_id'
        );
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseOrderItem::class
        );
    }
}
