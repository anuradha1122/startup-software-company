<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectRisk extends Model
{
    use LogsActivity;

    protected $fillable = [
        'project_id',
        'risk_number',
        'title',
        'description',
        'category',
        'likelihood',
        'impact',
        'score',
        'status',
        'owner_id',
        'mitigation',
        'contingency',
        'review_date',
        'escalated_at',
        'closed_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectRisk $risk
        ): void {
            if (! $risk->public_id) {
                $risk->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'review_date' => 'date',
            'escalated_at' => 'datetime',
            'closed_at' => 'datetime',
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

    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_id'
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('project-risks')
            ->logOnly([
                'risk_number',
                'title',
                'likelihood',
                'impact',
                'score',
                'status',
                'owner_id',
                'escalated_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
