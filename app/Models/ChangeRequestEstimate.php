<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ChangeRequestEstimate extends Model
{
    protected $fillable = [
        'change_request_id',
        'currency',
        'client_amount',
        'internal_estimated_cost',
        'estimated_hours',
        'estimated_days',
        'client_notes',
        'internal_notes',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ChangeRequestEstimate $estimate
        ): void {
            if (! $estimate->public_id) {
                $estimate->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'client_amount' => 'decimal:2',

            'internal_estimated_cost' => 'decimal:2',

            'estimated_hours' => 'decimal:2',

            'estimated_days' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function changeRequest(): BelongsTo
    {
        return $this->belongsTo(
            ChangeRequest::class
        );
    }
}
