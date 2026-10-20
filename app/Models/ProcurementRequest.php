<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProcurementRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_number',
        'project_id',
        'title',
        'business_reason',
        'priority',
        'status',
        'currency',
        'estimated_total',
        'requested_by',
        'submitted_at',
        'reviewed_by',
        'review_notes',
        'reviewed_at',
        'approved_at',
        'rejected_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (ProcurementRequest $request): void {
            $request->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'estimated_total' => 'decimal:2',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
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

    public function requester(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'requested_by'
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            ProcurementRequestItem::class
        );
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(
            PurchaseOrder::class
        );
    }
}
