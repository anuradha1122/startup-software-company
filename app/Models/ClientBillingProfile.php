<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClientBillingProfile extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'city',
        'country',
        'tax_number',
        'currency',
        'payment_terms_days',
        'invoice_notes',
        'is_default',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ClientBillingProfile $profile
        ): void {
            if (! $profile->public_id) {
                $profile->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'payment_terms_days' => 'integer',
            'is_default' => 'boolean',
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('client-billing')
            ->logOnly([
                'billing_name',
                'billing_email',
                'billing_phone',
                'country',
                'tax_number',
                'currency',
                'payment_terms_days',
                'is_default',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
