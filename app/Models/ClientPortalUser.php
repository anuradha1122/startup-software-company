<?php

namespace App\Models;

use App\Notifications\ClientPortalResetPasswordNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class ClientPortalUser extends Authenticatable implements CanResetPasswordContract
{
    use CanResetPassword;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'client_contact_id',
        'name',
        'email',
        'password',
        'status',
        'email_verified_at',
        'invited_at',
        'activated_at',
        'last_login_at',
        'invited_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (
            ClientPortalUser $user
        ): void {
            if (! $user->public_id) {
                $user->public_id =
                    (string) Str::ulid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',

            'invited_at' => 'datetime',

            'activated_at' => 'datetime',

            'last_login_at' => 'datetime',

            'password' => 'hashed',
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

    public function contact(): BelongsTo
    {
        return $this->belongsTo(
            ClientContact::class,
            'client_contact_id'
        );
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'invited_by'
        );
    }

    public function approvalResponses(): HasMany
    {
        return $this->hasMany(
            ClientApprovalRequest::class,
            'responded_by'
        );
    }

    public function sendPasswordResetNotification(
        $token
    ): void {
        $this->notify(
            new ClientPortalResetPasswordNotification(
                $token
            )
        );
    }
}
