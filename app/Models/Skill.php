<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'category',
        'active',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            Skill $skill
        ): void {
            if (! $skill->public_id) {
                $skill->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function resourceProfiles(): BelongsToMany
    {
        return $this->belongsToMany(
            ResourceProfile::class,
            'resource_profile_skill'
        )
            ->withPivot([
                'proficiency',
                'years_experience',
            ])
            ->withTimestamps();
    }
}
