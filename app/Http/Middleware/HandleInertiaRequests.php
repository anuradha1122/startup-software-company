<?php

namespace App\Http\Middleware;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),

            'name' => config('app.name'),

            'auth' => [
                'user' => $user,
                'roles' => fn () => $user
                    ? $user->getRoleNames()->values()
                    : [],

                'permissions' => fn () => $user
                    ? $user->getAllPermissions()
                        ->pluck('name')
                        ->values()
                    : [],
            ],

            'company' => fn () => Schema::hasTable('company_settings')
                    ? CompanySetting::query()
                        ->first()
                        ?->only([
                            'legal_name',
                            'trading_name',
                            'logo_path',
                            'currency',
                        ])
                    : null,

            'flash' => [
                'success' => fn () => $request->session()->get('success'),

                'error' => fn () => $request->session()->get('error'),
            ],

            'sidebarOpen' => ! $request->hasCookie('sidebar_state')
                || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
