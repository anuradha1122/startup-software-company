<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Contract extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'contract_number',
        'quotation_id',
        'client_id',
        'requirement_version_id',
        'estimate_id',
        'status',
        'title',
        'currency',
        'contract_value',
        'start_date',
        'end_date',
        'payment_terms_days',
        'payment_schedule',
        'special_terms',
        'termination_terms',
        'created_by',
        'issued_at',
        'signed_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Contract $contract): void {
            if (! $contract->public_id) {
                $contract->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'contract_value' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'issued_at' => 'datetime',
            'signed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(
            Quotation::class
        );
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(
            Client::class
        );
    }

    public function requirementVersion(): BelongsTo
    {
        return $this->belongsTo(
            RequirementVersion::class
        );
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(
            Estimate::class
        );
    }

    public function documents(): HasMany
    {
        return $this->hasMany(
            ContractDocument::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('contracts')
            ->logOnly([
                'contract_number',
                'status',
                'contract_value',
                'start_date',
                'end_date',
                'signed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function project(): HasOne
    {
        return $this->hasOne(
            Project::class
        );
    }
}
