<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeProfile extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'employee_number',
        'user_id',
        'employment_type',
        'status',
        'department',
        'job_title',
        'hire_date',
        'probation_end_date',
        'termination_date',
        'work_location',
        'emergency_contact_name',
        'emergency_contact_phone',
        'hr_notes',
        'created_by',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (EmployeeProfile $employee): void {
            $employee->public_id ??=
                (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'probation_end_date' => 'date',
            'termination_date' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function compensations(): HasMany
    {
        return $this->hasMany(
            EmployeeCompensation::class
        );
    }

    public function activeCompensation(): HasOne
    {
        return $this->hasOne(
            EmployeeCompensation::class
        )->where('active', true)
            ->latestOfMany(
                'effective_from'
            );
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(
            LeaveBalance::class
        );
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(
            LeaveRequest::class
        );
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(
            AttendanceRecord::class
        );
    }

    public function payrollEntries(): HasMany
    {
        return $this->hasMany(
            PayrollEntry::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('employee-profiles')
            ->logOnly([
                'employee_number',
                'user_id',
                'employment_type',
                'status',
                'department',
                'job_title',
                'hire_date',
                'termination_date',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
