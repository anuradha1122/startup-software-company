<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClientPortalDocument extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'project_id',
        'category',
        'title',
        'description',
        'original_name',
        'disk',
        'path',
        'mime_type',
        'size',
        'published_at',
        'expires_at',
        'uploaded_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ClientPortalDocument $document
        ): void {
            if (! $document->public_id) {
                $document->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'size' => 'integer',

            'published_at' => 'datetime',

            'expires_at' => 'datetime',
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class
        );
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }

    public function isAvailable(): bool
    {
        if ($this->published_at->isFuture()) {
            return false;
        }

        if (
            $this->expires_at
            && $this->expires_at->isPast()
        ) {
            return false;
        }

        return true;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(
                'client-portal-documents'
            )
            ->logOnly([
                'client_id',
                'project_id',
                'category',
                'title',
                'published_at',
                'expires_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
