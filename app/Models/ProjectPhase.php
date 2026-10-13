<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProjectPhase extends Model
{
    protected $fillable = [
        'project_id',
        'sort_order',
        'name',
        'description',
        'status',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectPhase $phase
        ): void {
            if (! $phase->public_id) {
                $phase->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'planned_start_date' => 'date',
            'planned_end_date' => 'date',
            'actual_start_date' => 'date',
            'actual_end_date' => 'date',
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

    public function milestones(): HasMany
    {
        return $this->hasMany(
            ProjectMilestone::class
        );
    }
}
