<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentAllocation extends Model
{
    protected $fillable = [
        'payment_id',
        'invoice_id',
        'amount',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            PaymentAllocation $allocation
        ): void {
            if (! $allocation->public_id) {
                $allocation->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            Payment::class
        );
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(
            Invoice::class
        );
    }
}
