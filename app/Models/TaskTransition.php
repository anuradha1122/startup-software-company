<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTransition extends Model
{
    protected $fillable = [
        'project_task_id',
        'from_status_id',
        'to_status_id',
        'changed_by',
        'notes',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(
            ProjectTask::class,
            'project_task_id'
        );
    }

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(
            TaskWorkflowStatus::class,
            'from_status_id'
        );
    }

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(
            TaskWorkflowStatus::class,
            'to_status_id'
        );
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'changed_by'
        );
    }
}
