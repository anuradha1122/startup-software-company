<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LeaveBalance extends Model
{
    protected $fillable = [
        'employee_profile_id',
        'leave_type_id',
        'year',
        'opening_balance',
        'accrued',
        'used',
        'adjusted',
        'available',
    ];

    protected static function booted(): void
    {
        static::creating(function (LeaveBalance $balance): void {
            $balance->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'accrued' => 'decimal:2',
            'used' => 'decimal:2',
            'adjusted' => 'decimal:2',
            'available' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            EmployeeProfile::class,
            'employee_profile_id'
        );
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(
            LeaveType::class
        );
    }
}
