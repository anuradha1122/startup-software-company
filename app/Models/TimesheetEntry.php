<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TimesheetEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'timesheet_id',
        'project_id',
        'project_task_id',
        'project_assignment_id',
        'work_date',
        'hours',
        'description',
        'billable',
        'hourly_cost_rate_snapshot',
        'internal_cost_amount',
        'cost_currency',
        'costed_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            TimesheetEntry $entry
        ): void {
            if (! $entry->public_id) {
                $entry->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'work_date' => 'date',

            'hours' => 'decimal:2',

            'billable' => 'boolean',

            'hourly_cost_rate_snapshot' => 'decimal:2',

            'internal_cost_amount' => 'decimal:2',

            'costed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(
            Timesheet::class
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class
        );
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(
            ProjectTask::class,
            'project_task_id'
        );
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(
            ProjectAssignment::class,
            'project_assignment_id'
        );
    }
}
