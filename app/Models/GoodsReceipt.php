<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GoodsReceipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'purchase_order_id',
        'received_date',
        'delivery_reference',
        'notes',
        'received_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (GoodsReceipt $receipt): void {
            $receipt->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
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

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'received_by'
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            GoodsReceiptItem::class
        );
    }
}
