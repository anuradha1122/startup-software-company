<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PayrollEntry extends Model
{
    protected $fillable = [
        'payroll_period_id',
        'employee_profile_id',
        'currency',
        'basic_salary',
        'allowances',
        'overtime_amount',
        'gross_pay',
        'deductions',
        'net_pay',
        'attendance_days',
        'leave_days',
        'unpaid_leave_days',
        'calculated_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (PayrollEntry $entry): void {
            $entry->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'basic_salary' => 'decimal:2',
            'allowances' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'gross_pay' => 'decimal:2',
            'deductions' => 'decimal:2',
            'net_pay' => 'decimal:2',
            'attendance_days' => 'decimal:2',
            'leave_days' => 'decimal:2',
            'unpaid_leave_days' => 'decimal:2',
            'calculated_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            EmployeeProfile::class,
            'employee_profile_id'
        );
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(
            PayrollPeriod::class,
            'payroll_period_id'
        );
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(
            PayrollAdjustment::class
        );
    }
}
