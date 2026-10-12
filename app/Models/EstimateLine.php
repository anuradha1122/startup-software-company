<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstimateLine extends Model
{
    protected $fillable = [
        'estimate_id',
        'sort_order',
        'module_name',
        'description',
        'estimated_hours',
        'billing_rate',
        'cost_rate',
        'revenue_amount',
        'cost_amount',
    ];

    protected function casts(): array
    {
        return [
            'estimated_hours' => 'decimal:2',

            'billing_rate' => 'decimal:2',

            'cost_rate' => 'decimal:2',

            'revenue_amount' => 'decimal:2',

            'cost_amount' => 'decimal:2',
        ];
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(
            Estimate::class
        );
    }
}
