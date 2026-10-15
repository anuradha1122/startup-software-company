<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'project_number',
        'contract_id',
        'client_id',
        'requirement_version_id',
        'estimate_id',
        'name',
        'status',
        'health',
        'methodology',
        'description',
        'currency',
        'contract_value',
        'project_manager_id',
        'client_sponsor_contact_id',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'activated_at',
        'completed_at',
        'approved_charter_id',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (Project $project): void {
            if (! $project->public_id) {
                $project->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'contract_value' => 'decimal:2',
            'planned_start_date' => 'date',
            'planned_end_date' => 'date',
            'actual_start_date' => 'date',
            'actual_end_date' => 'date',
            'activated_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(
            Contract::class
        );
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(
            Client::class
        );
    }

    public function requirementVersion(): BelongsTo
    {
        return $this->belongsTo(
            RequirementVersion::class
        );
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(
            Estimate::class
        );
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'project_manager_id'
        );
    }

    public function clientSponsor(): BelongsTo
    {
        return $this->belongsTo(
            ClientContact::class,
            'client_sponsor_contact_id'
        );
    }

    public function charters(): HasMany
    {
        return $this->hasMany(
            ProjectCharter::class
        );
    }

    public function approvedCharter(): BelongsTo
    {
        return $this->belongsTo(
            ProjectCharter::class,
            'approved_charter_id'
        );
    }

    public function phases(): HasMany
    {
        return $this->hasMany(
            ProjectPhase::class
        )->orderBy('sort_order');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(
            ProjectMilestone::class
        )->orderBy('due_date');
    }

    public function stakeholders(): HasMany
    {
        return $this->hasMany(
            ProjectStakeholder::class
        );
    }

    public function risks(): HasMany
    {
        return $this->hasMany(
            ProjectRisk::class
        );
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(
            ProjectDecision::class
        )->latest('decided_at');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(
            ProjectTask::class
        );
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(
            ProjectAssignment::class
        );
    }

    public function timesheetEntries(): HasMany
    {
        return $this->hasMany(
            TimesheetEntry::class
        );
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(
            ProjectExpense::class
        );
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(
            Invoice::class
        );
    }

    public function financeSetting(): HasOne
    {
        return $this->hasOne(
            ProjectFinanceSetting::class
        );
    }

    public function financialSnapshots(): HasMany
    {
        return $this->hasMany(
            ProjectFinancialSnapshot::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('projects')
            ->logOnly([
                'project_number',
                'name',
                'status',
                'health',
                'project_manager_id',
                'planned_start_date',
                'planned_end_date',
                'approved_charter_id',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
