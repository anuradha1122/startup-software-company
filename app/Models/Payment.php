<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'payment_number',
        'client_id',
        'payment_date',
        'currency',
        'amount',
        'allocated_amount',
        'unallocated_amount',
        'method',
        'reference_number',
        'notes',
        'recorded_by',
        'receipt_sent_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            Payment $payment
        ): void {
            if (! $payment->public_id) {
                $payment->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
            'allocated_amount' => 'decimal:2',
            'unallocated_amount' => 'decimal:2',
            'receipt_sent_at' => 'datetime',
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

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by'
        );
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(
            PaymentAllocation::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('payments')
            ->logOnly([
                'payment_number',
                'client_id',
                'payment_date',
                'currency',
                'amount',
                'allocated_amount',
                'unallocated_amount',
                'method',
                'reference_number',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
