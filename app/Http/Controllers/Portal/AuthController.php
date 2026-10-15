<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function create(): Response
    {
        return Inertia::render(
            'portal/auth/login'
        );
    }

    public function store(
        LoginRequest $request
    ): RedirectResponse {
        $credentials = [
            'email' => strtolower(
                $request->validated(
                    'email'
                )
            ),

            'password' => $request->validated(
                'password'
            ),

            'status' => 'active',
        ];

        if (
            ! Auth::guard(
                'client_portal'
            )->attempt(
                $credentials,
                $request->boolean(
                    'remember'
                )
            )
        ) {
            throw ValidationException::withMessages([
                'email' => 'The provided portal credentials are invalid.',
            ]);
        }

        $request
            ->session()
            ->regenerate();

        $user =
            Auth::guard(
                'client_portal'
            )->user();

        $user->update([
            'last_login_at' => now(),
        ]);

        return redirect()
            ->intended(
                route(
                    'portal.dashboard'
                )
            );
    }

    public function destroy(): RedirectResponse
    {
        Auth::guard(
            'client_portal'
        )->logout();

        request()
            ->session()
            ->invalidate();

        request()
            ->session()
            ->regenerateToken();

        return redirect()
            ->route(
                'portal.login'
            );
    }
}
