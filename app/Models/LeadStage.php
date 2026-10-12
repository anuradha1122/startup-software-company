<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadStage extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'probability',
        'is_won',
        'is_lost',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'is_won' => 'boolean',
            'is_lost' => 'boolean',
            'active' => 'boolean',
            'probability' => 'integer',
        ];
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'stage_id');
    }
}
