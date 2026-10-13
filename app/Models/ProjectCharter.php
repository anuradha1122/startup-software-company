<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectCharter extends Model
{
    use LogsActivity;

    protected $fillable = [
        'project_id',
        'version_number',
        'status',
        'business_objective',
        'project_objective',
        'governance_structure',
        'communication_plan',
        'escalation_process',
        'success_criteria',
        'assumptions',
        'constraints',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectCharter $charter
        ): void {
            if (! $charter->public_id) {
                $charter->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'success_criteria' => 'array',
            'assumptions' => 'array',
            'constraints' => 'array',
            'approved_at' => 'datetime',
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

    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('project-charters')
            ->logOnly([
                'version_number',
                'status',
                'business_objective',
                'project_objective',
                'governance_structure',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
