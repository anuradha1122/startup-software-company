<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WorkSubmissionAttachment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'work_submission_id',
        'title',
        'original_name',
        'disk',
        'path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            WorkSubmissionAttachment $attachment
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

    public function submission(): BelongsTo
    {
        return $this->belongsTo(
            WorkSubmission::class,
            'work_submission_id'
        );
    }
}
