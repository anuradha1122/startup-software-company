<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeCompensation extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'employee_compensations';

    protected $fillable = [
        'employee_profile_id',
        'currency',
        'monthly_basic_salary',
        'fixed_allowance',
        'fixed_deduction',
        'effective_from',
        'effective_to',
        'active',
        'notes',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            EmployeeCompensation $compensation
        ): void {
            $compensation->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'monthly_basic_salary' => 'decimal:2',

            'fixed_allowance' => 'decimal:2',

            'fixed_deduction' => 'decimal:2',

            'effective_from' => 'date',

            'effective_to' => 'date',

            'active' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            EmployeeProfile::class,
            'employee_profile_id'
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(
                'employee-compensation'
            )
            ->logOnly([
                'employee_profile_id',
                'currency',
                'monthly_basic_salary',
                'fixed_allowance',
                'fixed_deduction',
                'effective_from',
                'effective_to',
                'active',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
