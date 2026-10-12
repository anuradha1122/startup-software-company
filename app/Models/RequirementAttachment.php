<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class RequirementAttachment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'requirement_version_id',
        'title',
        'original_name',
        'disk',
        'path',
        'mime_type',
        'size',
        'visibility',
        'uploaded_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            RequirementAttachment $attachment
        ): void {
            if (! $attachment->public_id) {
                $attachment->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(
            RequirementVersion::class,
            'requirement_version_id'
        );
    }
}
