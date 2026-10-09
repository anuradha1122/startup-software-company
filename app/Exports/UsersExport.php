<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;

class UsersExport implements FromArray
{
    public function array(): array
    {
        $rows = [
            [
                'Public ID',
                'Name',
                'Email',
                'Phone',
                'Status',
                'Roles',
                'Email Verified',
                'Last Login',
                'Created At',
            ],
        ];

        User::query()
            ->with('roles')
            ->orderBy('name')
            ->each(function (User $user) use (&$rows): void {
                $rows[] = [
                    $user->public_id,
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->status,
                    $user->getRoleNames()->implode(', '),
                    $user->email_verified_at
                        ? 'Yes'
                        : 'No',
                    $user->last_login_at
                        ?->format('Y-m-d H:i:s'),
                    $user->created_at
                        ?->format('Y-m-d H:i:s'),
                ];
            });

        return $rows;
    }
}
