<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'client_number',
        'name',
        'legal_name',
        'registration_number',
        'tax_number',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'country',
        'default_currency',
        'default_payment_terms_days',
        'status',
        'notes',
        'onboarded_at',
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

    protected function casts(): array
    {
        return [
            'default_payment_terms_days' => 'integer',
            'onboarded_at' => 'datetime',
        ];
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

    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(
            ClientContact::class
        );
    }

    public function billingProfiles(): HasMany
    {
        return $this->hasMany(
            ClientBillingProfile::class
        );
    }

    public function documents(): HasMany
    {
        return $this->hasMany(
            ClientDocument::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('clients')
            ->logOnly([
                'client_number',
                'name',
                'legal_name',
                'registration_number',
                'tax_number',
                'email',
                'phone',
                'website',
                'country',
                'default_currency',
                'default_payment_terms_days',
                'status',
                'owner_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
