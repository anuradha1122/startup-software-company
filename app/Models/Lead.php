<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Lead extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'lead_number',
        'title',
        'company_name',
        'stage_id',
        'source_id',
        'owner_id',
        'estimated_value',
        'currency',
        'probability',
        'priority',
        'status',
        'next_follow_up_at',
        'last_activity_at',
        'follow_up_reminded_at',
        'stale_notified_at',
        'notes',
        'converted_at',
        'converted_client_id',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Lead $lead): void {
            if (! $lead->public_id) {
                $lead->public_id = (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'probability' => 'integer',
            'next_follow_up_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'follow_up_reminded_at' => 'datetime',
            'stale_notified_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(
            LeadStage::class,
            'stage_id'
        );
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(
            LeadSource::class,
            'source_id'
        );
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(LeadContact::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function convertedClient(): BelongsTo
    {
        return $this->belongsTo(
            Client::class,
            'converted_client_id'
        );
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('crm-leads')
            ->logOnly([
                'lead_number',
                'title',
                'company_name',
                'stage_id',
                'source_id',
                'owner_id',
                'estimated_value',
                'probability',
                'priority',
                'status',
                'next_follow_up_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
