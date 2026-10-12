<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RequirementApproval extends Model
{
    protected $fillable = [
        'requirement_version_id',
        'client_contact_id',
        'approver_name',
        'approver_email',
        'status',
        'requested_by',
        'requested_at',
        'recorded_by',
        'decision_notes',
        'responded_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            RequirementApproval $approval
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
            'responded_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(
            RequirementVersion::class,
            'requirement_version_id'
        );
    }

    public function clientContact(): BelongsTo
    {
        return $this->belongsTo(
            ClientContact::class
        );
    }
}
