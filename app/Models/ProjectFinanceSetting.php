<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectFinanceSetting extends Model
{
    use LogsActivity;

    protected $fillable = [
        'project_id',
        'target_margin_percent',
        'warning_margin_percent',
        'alerts_enabled',
        'last_margin_alerted_at',
        'created_by',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectFinanceSetting $setting
        ): void {
            if (! $setting->public_id) {
                $setting->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'target_margin_percent' => 'decimal:2',

            'warning_margin_percent' => 'decimal:2',

            'alerts_enabled' => 'boolean',

            'last_margin_alerted_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(
                'project-finance-settings'
            )
            ->logOnly([
                'project_id',
                'target_margin_percent',
                'warning_margin_percent',
                'alerts_enabled',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
