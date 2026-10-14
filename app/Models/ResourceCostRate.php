<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ResourceCostRate extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'currency',
        'hourly_cost_rate',
        'effective_from',
        'effective_to',
        'active',
        'notes',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ResourceCostRate $rate
        ): void {
            if (! $rate->public_id) {
                $rate->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'hourly_cost_rate' => 'decimal:2',

            'effective_from' => 'date',

            'effective_to' => 'date',

            'active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(
                'resource-cost-rates'
            )
            ->logOnly([
                'user_id',
                'currency',
                'hourly_cost_rate',
                'effective_from',
                'effective_to',
                'active',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
