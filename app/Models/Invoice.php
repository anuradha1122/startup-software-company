<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'project_id',
        'billing_contact_id',
        'status',
        'invoice_date',
        'due_date',
        'currency',
        'billing_name',
        'billing_email',
        'billing_address',
        'notes',
        'payment_instructions',
        'subtotal',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'balance_due',
        'issued_at',
        'last_sent_at',
        'paid_at',
        'voided_at',
        'created_by',
        'issued_by',
        'voided_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            Invoice $invoice
        ): void {
            if (! $invoice->public_id) {
                $invoice->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',

            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'balance_due' => 'decimal:2',

            'issued_at' => 'datetime',
            'last_sent_at' => 'datetime',
            'paid_at' => 'datetime',
            'voided_at' => 'datetime',
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class
        );
    }

    public function billingContact(): BelongsTo
    {
        return $this->belongsTo(
            ClientContact::class,
            'billing_contact_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'issued_by'
        );
    }

    public function lines(): HasMany
    {
        return $this->hasMany(
            InvoiceLine::class
        )->orderBy('sort_order');
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(
            PaymentAllocation::class
        );
    }

    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    public function isOutstanding(): bool
    {
        return in_array(
            $this->status,
            [
                'issued',
                'partially_paid',
                'overdue',
            ],
            true
        ) && (float) $this->balance_due > 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('invoices')
            ->logOnly([
                'invoice_number',
                'client_id',
                'project_id',
                'status',
                'invoice_date',
                'due_date',
                'currency',
                'total_amount',
                'amount_paid',
                'balance_due',
                'issued_at',
                'paid_at',
                'voided_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
