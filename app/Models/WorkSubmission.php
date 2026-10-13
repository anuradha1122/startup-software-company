<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WorkSubmission extends Model
{
    use LogsActivity;

    protected $fillable = [
        'project_task_id',
        'version_number',
        'status',
        'summary',
        'testing_notes',
        'deployment_notes',
        'reference_url',
        'submitted_by',
        'submitted_at',
        'reviewed_by',
        'review_notes',
        'reviewed_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            WorkSubmission $submission
        ): void {
            if (! $submission->public_id) {
                $submission->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(
            ProjectTask::class,
            'project_task_id'
        );
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'submitted_by'
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(
            WorkSubmissionAttachment::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('work-submissions')
            ->logOnly([
                'version_number',
                'status',
                'submitted_by',
                'reviewed_by',
                'reviewed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
