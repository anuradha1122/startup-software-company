<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_order_number',
        'procurement_request_id',
        'supplier_id',
        'project_id',
        'currency',
        'order_date',
        'expected_delivery_date',
        'status',
        'subtotal',
        'tax_amount',
        'total',
        'supplier_reference',
        'notes',
        'created_by',
        'issued_by',
        'issued_at',
        'cancelled_by',
        'cancelled_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $order): void {
            $order->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_delivery_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'issued_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(
            ProcurementRequest::class,
            'procurement_request_id'
        );
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            PurchaseOrderItem::class
        );
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(
            GoodsReceipt::class
        );
    }
}
