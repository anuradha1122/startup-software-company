<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Requirement extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'requirement_number',
        'client_id',
        'title',
        'status',
        'owner_id',
        'created_by',
        'approved_at',
        'approved_version_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            Requirement $requirement
        ): void {
            if (! $requirement->public_id) {
                $requirement->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(
            Client::class
        );
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function versions(): HasMany
    {
        return $this->hasMany(
            RequirementVersion::class
        );
    }

    public function latestVersion(): HasOne
    {
        return $this
            ->hasOne(
                RequirementVersion::class
            )
            ->latestOfMany(
                'version_number'
            );
    }

    public function approvedVersion(): BelongsTo
    {
        return $this->belongsTo(
            RequirementVersion::class,
            'approved_version_id'
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('requirements')
            ->logOnly([
                'requirement_number',
                'client_id',
                'title',
                'status',
                'owner_id',
                'approved_version_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
