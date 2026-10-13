<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProjectMilestone extends Model
{
    protected $fillable = [
        'project_id',
        'project_phase_id',
        'name',
        'description',
        'due_date',
        'status',
        'client_visible',
        'completed_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectMilestone $milestone
        ): void {
            if (! $milestone->public_id) {
                $milestone->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'client_visible' => 'boolean',
            'completed_at' => 'datetime',
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

    public function phase(): BelongsTo
    {
        return $this->belongsTo(
            ProjectPhase::class,
            'project_phase_id'
        );
    }
}
