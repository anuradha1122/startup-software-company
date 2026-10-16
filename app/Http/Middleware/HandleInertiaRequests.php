<?php

namespace App\Http\Middleware;

use App\Models\ClientPortalUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(
        Request $request
    ): array {
        $authenticatedUser =
            $request->user();

        $internalUser =
            $authenticatedUser instanceof User
                ? $authenticatedUser
                : null;

        $portalUser =
            Auth::guard(
                'client_portal'
            )->user();

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $internalUser,

                'roles' => fn () => $internalUser
                            ? $internalUser
                                ->getRoleNames()
                                ->values()
                                ->all()
                            : [],

                'permissions' => fn () => $internalUser
                            ? $internalUser
                                ->getAllPermissions()
                                ->pluck('name')
                                ->values()
                                ->all()
                            : [],

                'portalUser' => $portalUser instanceof ClientPortalUser
                        ? [
                            'public_id' => $portalUser
                                ->public_id,

                            'name' => $portalUser
                                ->name,

                            'email' => $portalUser
                                ->email,

                            'client_id' => $portalUser
                                ->client_id,
                        ]
                        : null,
            ],
        ];
    }
}
