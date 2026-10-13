<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTaskDependency extends Model
{
    protected $fillable = [
        'project_task_id',
        'depends_on_task_id',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(
            ProjectTask::class,
            'project_task_id'
        );
    }

    public function dependency(): BelongsTo
    {
        return $this->belongsTo(
            ProjectTask::class,
            'depends_on_task_id'
        );
    }
}
