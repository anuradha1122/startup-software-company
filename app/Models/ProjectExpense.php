<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectExpense extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'expense_number',
        'project_id',
        'supplier_id',
        'type',
        'title',
        'description',
        'expense_date',
        'reference_number',
        'currency',
        'subtotal',
        'tax_amount',
        'total_amount',
        'billable_to_client',
        'status',
        'incurred_by',
        'created_by',
        'submitted_at',
        'reviewed_by',
        'review_notes',
        'reviewed_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectExpense $expense
        ): void {
            if (! $expense->public_id) {
                $expense->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',

            'subtotal' => 'decimal:2',

            'tax_amount' => 'decimal:2',

            'total_amount' => 'decimal:2',

            'billable_to_client' => 'boolean',

            'submitted_at' => 'datetime',

            'reviewed_at' => 'datetime',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(
            Supplier::class
        );
    }

    public function incurredBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'incurred_by'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(
            ProjectExpenseAttachment::class
        );
    }

    public function isEditable(): bool
    {
        return in_array(
            $this->status,
            [
                'draft',
                'rejected',
            ],
            true
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(
                'project-expenses'
            )
            ->logOnly([
                'expense_number',
                'project_id',
                'supplier_id',
                'type',
                'title',
                'expense_date',
                'currency',
                'subtotal',
                'tax_amount',
                'total_amount',
                'billable_to_client',
                'status',
                'submitted_at',
                'reviewed_by',
                'reviewed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
