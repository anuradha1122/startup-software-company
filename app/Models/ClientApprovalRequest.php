<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ClientApprovalRequest extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'approval_number',
        'client_id',
        'project_id',
        'type',
        'title',
        'summary',
        'source_type',
        'source_id',
        'status',
        'requested_by',
        'requested_at',
        'due_at',
        'responded_by',
        'response_comment',
        'responded_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ClientApprovalRequest $approval
        ): void {
            if (! $approval->public_id) {
                $approval->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',

            'due_at' => 'datetime',

            'responded_at' => 'datetime',
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

    public function requester(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'requested_by'
        );
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(
            ClientPortalUser::class,
            'responded_by'
        );
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(
                'client-approvals'
            )
            ->logOnly([
                'approval_number',
                'client_id',
                'project_id',
                'type',
                'title',
                'status',
                'requested_at',
                'due_at',
                'responded_by',
                'responded_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
