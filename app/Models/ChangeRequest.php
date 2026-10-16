<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ChangeRequest extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'change_request_number',
        'client_id',
        'project_id',
        'client_portal_user_id',
        'title',
        'description',
        'business_reason',
        'impact_summary',
        'internal_analysis',
        'priority',
        'status',
        'submitted_at',
        'reviewed_by',
        'review_notes',
        'reviewed_at',
        'approved_at',
        'rejected_at',
        'implemented_at',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ChangeRequest $request
        ): void {
            if (! $request->public_id) {
                $request->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',

            'reviewed_at' => 'datetime',

            'approved_at' => 'datetime',

            'rejected_at' => 'datetime',

            'implemented_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(
            Client::class
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class
        );
    }

    public function portalUser(): BelongsTo
    {
        return $this->belongsTo(
            ClientPortalUser::class,
            'client_portal_user_id'
        );
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function estimate(): HasOne
    {
        return $this->hasOne(
            ChangeRequestEstimate::class
        );
    }

    public function isEditable(): bool
    {
        return in_array(
            $this->status,
            [
                'draft',
                'submitted',
                'under_review',
            ],
            true
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(
                'change-requests'
            )
            ->logOnly([
                'change_request_number',
                'project_id',
                'priority',
                'status',
                'submitted_at',
                'reviewed_by',
                'reviewed_at',
                'approved_at',
                'rejected_at',
                'implemented_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
