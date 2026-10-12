<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Proposal extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'proposal_number',
        'client_id',
        'requirement_version_id',
        'estimate_id',
        'title',
        'status',
        'executive_summary',
        'solution_overview',
        'delivery_approach',
        'commercial_notes',
        'valid_until',
        'created_by',
        'approved_by',
        'approved_at',
        'sent_at',
        'responded_at',
        'decision_notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (Proposal $proposal): void {
            if (! $proposal->public_id) {
                $proposal->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
            'approved_at' => 'datetime',
            'sent_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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

    public function quotation(): HasMany
    {
        return $this->hasMany(
            Quotation::class
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('proposals')
            ->logOnly([
                'proposal_number',
                'status',
                'valid_until',
                'approved_by',
                'sent_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
