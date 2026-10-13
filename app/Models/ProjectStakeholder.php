<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProjectStakeholder extends Model
{
    protected $fillable = [
        'project_id',
        'type',
        'user_id',
        'client_contact_id',
        'name',
        'email',
        'role',
        'responsibility',
        'is_primary',
        'receive_notifications',
        'active',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectStakeholder $stakeholder
        ): void {
            if (! $stakeholder->public_id) {
                $stakeholder->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'receive_notifications' => 'boolean',
            'active' => 'boolean',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function clientContact(): BelongsTo
    {
        return $this->belongsTo(
            ClientContact::class
        );
    }
}
