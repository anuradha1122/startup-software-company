<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InvoiceLine extends Model
{
    protected $fillable = [
        'invoice_id',
        'sort_order',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'subtotal',
        'tax_amount',
        'total_amount',
        'source_type',
        'source_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            InvoiceLine $line
        ): void {
            if (! $line->public_id) {
                $line->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'tax_rate' => 'decimal:4',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(
            Invoice::class
        );
    }
}
