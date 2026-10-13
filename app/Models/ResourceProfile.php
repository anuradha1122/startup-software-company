<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ResourceProfile extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'job_title',
        'department',
        'capacity_hours_per_week',
        'default_allocation_percent',
        'seniority',
        'bio',
        'available_for_projects',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ResourceProfile $profile
        ): void {
            if (! $profile->public_id) {
                $profile->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'capacity_hours_per_week' => 'integer',

            'default_allocation_percent' => 'integer',

            'available_for_projects' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'resource_profile_skill'
        )
            ->withPivot([
                'proficiency',
                'years_experience',
            ])
            ->withTimestamps();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('resource-profiles')
            ->logOnly([
                'job_title',
                'department',
                'capacity_hours_per_week',
                'default_allocation_percent',
                'seniority',
                'available_for_projects',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
