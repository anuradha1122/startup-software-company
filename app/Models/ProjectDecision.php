<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProjectDecision extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'decision',
        'rationale',
        'decided_by',
        'decided_at',
        'client_visible',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectDecision $decision
        ): void {
            if (! $decision->public_id) {
                $decision->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'decided_at' => 'date',
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

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'decided_by'
        );
    }
}
