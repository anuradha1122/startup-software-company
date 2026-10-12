<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ClientDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'category',
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
            ClientDocument $document
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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }
}
