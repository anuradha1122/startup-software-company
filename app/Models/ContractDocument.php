<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ContractDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_id',
        'type',
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
            ContractDocument $document
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

    public function contract(): BelongsTo
    {
        return $this->belongsTo(
            Contract::class
        );
    }
}
