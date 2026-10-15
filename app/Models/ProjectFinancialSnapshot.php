<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProjectFinancialSnapshot extends Model
{
    protected $fillable = [
        'project_id',
        'snapshot_date',
        'currency',
        'contracted_revenue',
        'invoiced_revenue',
        'collected_cash',
        'labor_cost',
        'non_labor_cost',
        'actual_cost',
        'gross_profit',
        'gross_margin_percent',
        'unbilled_revenue',
        'outstanding_receivable',
        'collection_percent',
        'cost_consumption_percent',
        'invoice_progress_percent',
        'currency_mismatch_count',
        'generated_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ProjectFinancialSnapshot $snapshot
        ): void {
            if (! $snapshot->public_id) {
                $snapshot->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',

            'contracted_revenue' => 'decimal:2',

            'invoiced_revenue' => 'decimal:2',

            'collected_cash' => 'decimal:2',

            'labor_cost' => 'decimal:2',

            'non_labor_cost' => 'decimal:2',

            'actual_cost' => 'decimal:2',

            'gross_profit' => 'decimal:2',

            'gross_margin_percent' => 'decimal:4',

            'unbilled_revenue' => 'decimal:2',

            'outstanding_receivable' => 'decimal:2',

            'collection_percent' => 'decimal:4',

            'cost_consumption_percent' => 'decimal:4',

            'invoice_progress_percent' => 'decimal:4',

            'currency_mismatch_count' => 'integer',
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

    public function generator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'generated_by'
        );
    }
}
