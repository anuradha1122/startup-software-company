<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SupportTicketMessage extends Model
{
    protected $fillable = [
        'support_ticket_id',
        'author_type',
        'user_id',
        'client_portal_user_id',
        'message',
        'internal_only',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            SupportTicketMessage $message
        ): void {
            if (! $message->public_id) {
                $message->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'internal_only' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(
            SupportTicket::class,
            'support_ticket_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function portalUser(): BelongsTo
    {
        return $this->belongsTo(
            ClientPortalUser::class,
            'client_portal_user_id'
        );
    }
}
