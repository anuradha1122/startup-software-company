<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LeaveType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'annual_entitlement',
        'paid',
        'requires_attachment',
        'allow_negative_balance',
        'active',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::creating(function (LeaveType $leaveType): void {
            $leaveType->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'annual_entitlement' => 'decimal:2',
            'paid' => 'boolean',
            'requires_attachment' => 'boolean',
            'allow_negative_balance' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
}
