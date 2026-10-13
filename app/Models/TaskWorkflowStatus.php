<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TaskWorkflowStatus extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'is_initial',
        'is_in_progress',
        'is_review',
        'is_completed',
        'active',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            TaskWorkflowStatus $status
        ): void {
            if (! $status->public_id) {
                $status->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_initial' => 'boolean',
            'is_in_progress' => 'boolean',
            'is_review' => 'boolean',
            'is_completed' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(
            ProjectTask::class,
            'workflow_status_id'
        );
    }
}
