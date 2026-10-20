<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_number',
        'asset_tag',
        'name',
        'category',
        'brand',
        'model',
        'serial_number',
        'supplier_id',
        'purchase_order_item_id',
        'project_id',
        'currency',
        'purchase_cost',
        'purchase_date',
        'warranty_expires_at',
        'status',
        'location',
        'notes',
        'created_by',
        'retired_at',
        'retired_by',
        'retirement_reason',
    ];

    protected static function booted(): void
    {
        static::creating(function (Asset $asset): void {
            $asset->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'purchase_cost' => 'decimal:2',
            'purchase_date' => 'date',
            'warranty_expires_at' => 'date',
            'retired_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
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

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(
            PurchaseOrderItem::class
        );
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(
            AssetAssignment::class
        );
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(
            AssetMaintenance::class
        );
    }
}
