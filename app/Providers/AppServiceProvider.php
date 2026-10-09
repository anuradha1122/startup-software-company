<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAuthentication();
        $this->configureLoginAudit();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
                ? Password::min(12)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
                : Password::min(8)
        );
    }

    protected function configureAuthentication(): void
    {
        Fortify::authenticateUsing(
            function (Request $request): ?User {
                $user = User::query()
                    ->where('email', mb_strtolower(
                        (string) $request->input('email')
                    ))
                    ->first();

                if (! $user) {
                    return null;
                }

                if (! $user->isActive()) {
                    return null;
                }

                if (! Hash::check(
                    (string) $request->input('password'),
                    $user->password
                )) {
                    return null;
                }

                return $user;
            }
        );
    }

    protected function configureLoginAudit(): void
    {
        Event::listen(
            Login::class,
            function (Login $event): void {
                if (! $event->user instanceof User) {
                    return;
                }

                $event->user->forceFill([
                    'last_login_at' => now(),
                    'last_login_ip' => request()->ip(),
                ])->saveQuietly();

                activity('authentication')
                    ->causedBy($event->user)
                    ->performedOn($event->user)
                    ->event('login')
                    ->withProperties([
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ])
                    ->log('User logged in');
            }
        );
    }
}
