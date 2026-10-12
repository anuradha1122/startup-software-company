<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Estimate extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'estimate_number',
        'requirement_version_id',
        'status',
        'currency',
        'contingency_percent',
        'total_hours',
        'estimated_revenue',
        'estimated_cost',
        'gross_margin',
        'gross_margin_percent',
        'assumptions',
        'internal_notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            Estimate $estimate
        ): void {
            if (! $estimate->public_id) {
                $estimate->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'contingency_percent' => 'decimal:2',

            'total_hours' => 'decimal:2',

            'estimated_revenue' => 'decimal:2',

            'estimated_cost' => 'decimal:2',

            'gross_margin' => 'decimal:2',

            'gross_margin_percent' => 'decimal:2',

            'approved_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function requirementVersion(): BelongsTo
    {
        return $this->belongsTo(
            RequirementVersion::class
        );
    }

    public function lines(): HasMany
    {
        return $this->hasMany(
            EstimateLine::class
        )->orderBy('sort_order');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('estimates')
            ->logOnly([
                'estimate_number',
                'status',
                'currency',
                'contingency_percent',
                'total_hours',
                'estimated_revenue',
                'estimated_cost',
                'gross_margin',
                'gross_margin_percent',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
