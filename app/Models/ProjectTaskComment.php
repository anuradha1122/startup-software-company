<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProjectTaskComment extends Model
{
    protected $fillable = [
        'project_task_id',
        'body',
        'client_visible',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectTaskComment $comment
        ): void {
            if (! $comment->public_id) {
                $comment->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'client_visible' => 'boolean',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(
            ProjectTask::class,
            'project_task_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}
