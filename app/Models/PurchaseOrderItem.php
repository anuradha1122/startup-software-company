<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'procurement_request_item_id',
        'description',
        'quantity',
        'unit',
        'unit_cost',
        'line_total',
        'received_quantity',
        'asset_trackable',
    ];

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrderItem $item): void {
            $item->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'line_total' => 'decimal:2',
            'received_quantity' => 'decimal:2',
            'asset_trackable' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseOrder::class,
            'purchase_order_id'
        );
    }

    public function requestItem(): BelongsTo
    {
        return $this->belongsTo(
            ProcurementRequestItem::class,
            'procurement_request_item_id'
        );
    }

    public function receiptItems(): HasMany
    {
        return $this->hasMany(
            GoodsReceiptItem::class
        );
    }

    public function assets(): HasMany
    {
        return $this->hasMany(
            Asset::class
        );
    }
}
