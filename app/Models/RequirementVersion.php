<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RequirementVersion extends Model
{
    use LogsActivity;

    protected $fillable = [
        'requirement_id',
        'version_number',
        'status',
        'version_title',
        'objective',
        'scope_in',
        'scope_out',
        'functional_requirements',
        'non_functional_requirements',
        'assumptions',
        'constraints',
        'acceptance_criteria',
        'client_notes',
        'internal_notes',
        'created_by',
        'submitted_for_review_at',
        'submitted_for_approval_at',
        'approved_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            RequirementVersion $version
        ): void {
            if (! $version->public_id) {
                $version->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'functional_requirements' => 'array',

            'non_functional_requirements' => 'array',

            'assumptions' => 'array',

            'constraints' => 'array',

            'acceptance_criteria' => 'array',

            'submitted_for_review_at' => 'datetime',

            'submitted_for_approval_at' => 'datetime',

            'approved_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(
            Requirement::class
        );
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(
            RequirementApproval::class
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(
            RequirementAttachment::class
        );
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(
            Estimate::class
        );
    }

    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(
                'requirement-versions'
            )
            ->logOnly([
                'version_number',
                'status',
                'version_title',
                'objective',
                'scope_in',
                'scope_out',
                'client_notes',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
