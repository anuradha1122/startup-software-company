<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PayrollPeriod extends Model
{
    protected $fillable = [
        'payroll_number',
        'name',
        'period_start',
        'period_end',
        'payment_date',
        'status',
        'calculated_at',
        'approved_by',
        'approved_at',
        'published_by',
        'published_at',
        'closed_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (PayrollPeriod $period): void {
            $period->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'payment_date' => 'date',
            'calculated_at' => 'datetime',
            'approved_at' => 'datetime',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function entries(): HasMany
    {
        return $this->hasMany(
            PayrollEntry::class
        );
    }
}
