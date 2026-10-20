<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProcurementRequestItem extends Model
{
    protected $fillable = [
        'procurement_request_id',
        'description',
        'quantity',
        'unit',
        'estimated_unit_cost',
        'estimated_total',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProcurementRequestItem $item): void {
            $item->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'estimated_unit_cost' => 'decimal:2',
            'estimated_total' => 'decimal:2',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(
            ProcurementRequest::class,
            'procurement_request_id'
        );
    }
}
