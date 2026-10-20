<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AssetMaintenance extends Model
{
    protected $fillable = [
        'asset_id',
        'type',
        'status',
        'started_date',
        'completed_date',
        'vendor',
        'currency',
        'cost',
        'description',
        'resolution',
        'created_by',
        'completed_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (AssetMaintenance $maintenance): void {
            $maintenance->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'started_date' => 'date',
            'completed_date' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(
            Asset::class
        );
    }
}
