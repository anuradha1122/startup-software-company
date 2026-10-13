<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectTask extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'task_sequence',
        'task_number',
        'parent_task_id',
        'project_phase_id',
        'project_milestone_id',
        'workflow_status_id',
        'title',
        'description',
        'type',
        'priority',
        'assignee_id',
        'estimated_hours',
        'planned_start_date',
        'due_date',
        'started_at',
        'completed_at',
        'sort_order',
        'client_visible',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectTask $task
        ): void {
            if (! $task->public_id) {
                $task->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'estimated_hours' => 'decimal:2',
            'planned_start_date' => 'date',
            'due_date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'client_visible' => 'boolean',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            ProjectTask::class,
            'parent_task_id'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            ProjectTask::class,
            'parent_task_id'
        );
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(
            ProjectPhase::class,
            'project_phase_id'
        );
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(
            ProjectMilestone::class,
            'project_milestone_id'
        );
    }

    public function workflowStatus(): BelongsTo
    {
        return $this->belongsTo(
            TaskWorkflowStatus::class,
            'workflow_status_id'
        );
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assignee_id'
        );
    }

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(
            ProjectTask::class,
            'project_task_dependencies',
            'project_task_id',
            'depends_on_task_id'
        )->withTimestamps();
    }

    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(
            ProjectTask::class,
            'project_task_dependencies',
            'depends_on_task_id',
            'project_task_id'
        )->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(
            ProjectTaskComment::class
        );
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(
            WorkSubmission::class
        );
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(
            TaskTransition::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('project-tasks')
            ->logOnly([
                'task_number',
                'title',
                'workflow_status_id',
                'priority',
                'assignee_id',
                'due_date',
                'completed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
