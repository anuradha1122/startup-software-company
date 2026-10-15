<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientPortalUserIsActive
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user =
            Auth::guard(
                'client_portal'
            )->user();

        if (
            ! $user
            || $user->status !== 'active'
        ) {
            Auth::guard(
                'client_portal'
            )->logout();

            $request
                ->session()
                ->invalidate();

            $request
                ->session()
                ->regenerateToken();

            return redirect()
                ->route(
                    'portal.login'
                );
        }

        return $next($request);
    }
}
