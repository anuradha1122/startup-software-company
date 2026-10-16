<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PayrollAdjustment extends Model
{
    protected $fillable = [
        'payroll_entry_id',
        'type',
        'label',
        'amount',
        'notes',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (PayrollAdjustment $adjustment): void {
            $adjustment->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(
            PayrollEntry::class,
            'payroll_entry_id'
        );
    }
}
