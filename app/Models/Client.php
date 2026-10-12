<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'source_lead_id',
        'owner_id',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Client $client): void {
            if (! $client->public_id) {
                $client->public_id = (string) Str::ulid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function sourceLead(): BelongsTo
    {
        return $this->belongsTo(
            Lead::class,
            'source_lead_id'
        );
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }
}
