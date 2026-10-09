<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Notifications\AccountStatusChangedNotification;
use App\Notifications\UserWelcomeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->with('roles:id,name')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/users/index', [
            'users' => $users,
            'roles' => Role::query()
                ->orderBy('name')
                ->pluck('name'),
        ]);
    }

    public function store(
        StoreUserRequest $request
    ): RedirectResponse {
        $temporaryPassword = Str::password(32);

        $user = User::query()->create([
            ...$request->safe()->only([
                'name',
                'email',
                'phone',
                'status',
            ]),
            'password' => $temporaryPassword,
            'created_by' => $request->user()->id,
        ]);

        $user->syncRoles($request->validated('roles'));

        activity('users')
            ->causedBy($request->user())
            ->performedOn($user)
            ->event('roles-assigned')
            ->withProperties([
                'roles' => $user->getRoleNames(),
            ])
            ->log('User roles assigned');

        $user->notify(
            new UserWelcomeNotification(
                $user->getRoleNames()->all()
            )
        );

        Password::sendResetLink([
            'email' => $user->email,
        ]);

        return back()->with(
            'success',
            'User created. Password setup instructions were sent.'
        );
    }

    public function edit(User $user): Response
    {
        Gate::authorize('update', $user);

        return Inertia::render('admin/users/edit', [
            'managedUser' => [
                'public_id' => $user->public_id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'roles' => $user->getRoleNames(),
            ],

            'roles' => Role::query()
                ->orderBy('name')
                ->pluck('name'),
        ]);
    }

    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        if (
            $request->user()->is($user)
            && $request->validated('status') !== 'active'
        ) {
            return back()->with(
                'error',
                'You cannot disable your own account.'
            );
        }

        $oldStatus = $user->status;

        $user->update(
            $request->safe()->only([
                'name',
                'email',
                'phone',
                'status',
            ])
        );

        $user->syncRoles(
            $request->validated('roles')
        );

        activity('users')
            ->causedBy($request->user())
            ->performedOn($user)
            ->event('roles-updated')
            ->withProperties([
                'roles' => $user->getRoleNames(),
            ])
            ->log('User roles updated');

        if ($oldStatus !== $user->status) {
            $user->notify(
                new AccountStatusChangedNotification(
                    $user->status
                )
            );
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    public function destroy(
        User $user
    ): RedirectResponse {
        Gate::authorize('delete', $user);

        if (request()->user()->is($user)) {
            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }

        $user->delete();

        return back()->with(
            'success',
            'User archived.'
        );
    }

    public function export(): BinaryFileResponse
    {
        Gate::authorize('viewAny', User::class);

        activity('exports')
            ->causedBy(request()->user())
            ->event('export')
            ->log('User list exported');

        return Excel::download(
            new UsersExport,
            'users-'.now()->format('Y-m-d-His').'.xlsx'
        );
    }
}
