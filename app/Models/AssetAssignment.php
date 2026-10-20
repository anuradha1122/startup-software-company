<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AssetAssignment extends Model
{
    protected $fillable = [
        'asset_id',
        'user_id',
        'project_id',
        'assigned_date',
        'expected_return_date',
        'returned_date',
        'status',
        'assignment_notes',
        'return_notes',
        'assigned_by',
        'returned_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (AssetAssignment $assignment): void {
            $assignment->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'assigned_date' => 'date',
            'expected_return_date' => 'date',
            'returned_date' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(
            Asset::class
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class
        );
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_by'
        );
    }

    public function returner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'returned_by'
        );
    }
}
