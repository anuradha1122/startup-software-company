<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClientContact extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'designation',
        'email',
        'phone',
        'is_primary',
        'is_billing_contact',
        'is_decision_maker',
        'status',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ClientContact $contact
        ): void {
            if (! $contact->public_id) {
                $contact->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_billing_contact' => 'boolean',
            'is_decision_maker' => 'boolean',
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
            ->useLogName('client-contacts')
            ->logOnly([
                'name',
                'designation',
                'email',
                'phone',
                'is_primary',
                'is_billing_contact',
                'is_decision_maker',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
