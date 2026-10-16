<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SupportTicket extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'client_id',
        'project_id',
        'client_portal_user_id',
        'subject',
        'description',
        'priority',
        'severity',
        'status',
        'assigned_to',
        'first_response_due_at',
        'resolution_due_at',
        'first_responded_at',
        'resolved_at',
        'closed_at',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            SupportTicket $ticket
        ): void {
            if (! $ticket->public_id) {
                $ticket->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'first_response_due_at' => 'datetime',

            'resolution_due_at' => 'datetime',

            'first_responded_at' => 'datetime',

            'resolved_at' => 'datetime',

            'closed_at' => 'datetime',
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

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'assigned_to'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function messages(): HasMany
    {
        return $this->hasMany(
            SupportTicketMessage::class
        )->orderBy('created_at');
    }

    public function isClosed(): bool
    {
        return in_array(
            $this->status,
            [
                'closed',
                'cancelled',
            ],
            true
        );
    }

    public function isResolved(): bool
    {
        return in_array(
            $this->status,
            [
                'resolved',
                'closed',
            ],
            true
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(
                'support-tickets'
            )
            ->logOnly([
                'ticket_number',
                'client_id',
                'project_id',
                'priority',
                'severity',
                'status',
                'assigned_to',
                'first_responded_at',
                'resolved_at',
                'closed_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
