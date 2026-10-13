<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectAssignment extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'user_id',
        'role',
        'allocation_percent',
        'status',
        'start_date',
        'end_date',
        'billable',
        'responsibilities',
        'notes',
        'created_by',
        'ended_by',
        'ended_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectAssignment $assignment
        ): void {
            if (! $assignment->public_id) {
                $assignment->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'allocation_percent' => 'integer',

            'start_date' => 'date',

            'end_date' => 'date',

            'billable' => 'boolean',

            'ended_at' => 'datetime',
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

    public function endedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'ended_by'
        );
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if (
            $this->start_date
            && $this->start_date->isFuture()
        ) {
            return false;
        }

        if (
            $this->end_date
            && $this->end_date->isPast()
        ) {
            return false;
        }

        return true;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(
                'project-assignments'
            )
            ->logOnly([
                'project_id',
                'user_id',
                'role',
                'allocation_percent',
                'status',
                'start_date',
                'end_date',
                'billable',
                'ended_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
