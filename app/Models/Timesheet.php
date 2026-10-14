<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Timesheet extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'week_start',
        'week_end',
        'status',
        'notes',
        'submitted_at',
        'reviewed_by',
        'review_notes',
        'reviewed_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            Timesheet $timesheet
        ): void {
            if (! $timesheet->public_id) {
                $timesheet->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function entries(): HasMany
    {
        return $this->hasMany(
            TimesheetEntry::class
        )->orderBy('work_date');
    }

    public function totalHours(): float
    {
        return (float) $this
            ->entries()
            ->sum('hours');
    }

    public function totalInternalCost(): float
    {
        return (float) $this
            ->entries()
            ->sum(
                'internal_cost_amount'
            );
    }

    public function isEditable(): bool
    {
        return in_array(
            $this->status,
            [
                'draft',
                'rejected',
            ],
            true
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('timesheets')
            ->logOnly([
                'status',
                'week_start',
                'week_end',
                'submitted_at',
                'reviewed_by',
                'reviewed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
