<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Quotation extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'quotation_number',
        'proposal_id',
        'client_id',
        'requirement_version_id',
        'estimate_id',
        'status',
        'currency',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'total_amount',
        'payment_terms_days',
        'payment_schedule',
        'terms_and_conditions',
        'notes',
        'valid_until',
        'created_by',
        'approved_by',
        'approved_at',
        'sent_at',
        'responded_at',
        'decision_notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation): void {
            if (! $quotation->public_id) {
                $quotation->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'valid_until' => 'date',
            'approved_at' => 'datetime',
            'sent_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(
            Proposal::class
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

    public function contract(): HasOne
    {
        return $this->hasOne(
            Contract::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('quotations')
            ->logOnly([
                'quotation_number',
                'status',
                'subtotal',
                'discount_percent',
                'tax_percent',
                'total_amount',
                'valid_until',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
