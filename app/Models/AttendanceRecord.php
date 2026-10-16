<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'employee_profile_id',
        'work_date',
        'clock_in_at',
        'clock_out_at',
        'worked_hours',
        'overtime_hours',
        'status',
        'source',
        'notes',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (AttendanceRecord $attendance): void {
            $attendance->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
            'worked_hours' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(
            EmployeeProfile::class,
            'employee_profile_id'
        );
    }
}
