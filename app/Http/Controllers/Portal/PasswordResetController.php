<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ClientPortalUser;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
{
    public function request(): Response
    {
        return Inertia::render(
            'portal/auth/forgot-password'
        );
    }

    public function email(
        Request $request
    ): RedirectResponse {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        Password::broker(
            'client_portal'
        )->sendResetLink(
            $request->only(
                'email'
            )
        );

        return back()->with(
            'status',
            'If the portal account exists, a password setup link has been sent.'
        );
    }

    public function reset(
        Request $request,
        string $token
    ): Response {
        return Inertia::render(
            'portal/auth/reset-password',
            [
                'token' => $token,

                'email' => $request->query(
                    'email'
                ),
            ]
        );
    }

    public function update(
        Request $request
    ): RedirectResponse {
        $data =
            $request->validate([
                'token' => [
                    'required',
                ],

                'email' => [
                    'required',
                    'email',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    PasswordRule::defaults(),
                ],
            ]);

        $status =
            Password::broker(
                'client_portal'
            )->reset(
                $data,
                function (
                    ClientPortalUser $user,
                    string $password
                ): void {
                    $user->forceFill([
                        'password' => Hash::make(
                            $password
                        ),

                        'status' => 'active',

                        'email_verified_at' => $user
                            ->email_verified_at
                            ?? now(),

                        'activated_at' => $user
                            ->activated_at
                            ?? now(),

                        'remember_token' => Str::random(60),
                    ])->save();

                    event(
                        new PasswordReset(
                            $user
                        )
                    );
                }
            );

        if (
            $status
            !== Password::PASSWORD_RESET
        ) {
            return back()
                ->withErrors([
                    'email' => __($status),
                ]);
        }

        return redirect()
            ->route(
                'portal.login'
            )
            ->with(
                'status',
                'Your portal password has been set.'
            );
    }
}
