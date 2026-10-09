<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private const CORE_ROLES = [
        'Director',
        'Admin',
        'Sales',
        'Project Manager',
        'Finance',
        'HR',
        'Employee',
        'Freelancer',
        'Client',
    ];

    public function index(Request $request): Response
    {
        abort_unless(
            $request->user()->can('roles.manage'),
            403
        );

        return Inertia::render('admin/roles/index', [
            'roles' => Role::query()
                ->with('permissions:id,name')
                ->orderBy('name')
                ->get(),

            'permissions' => Permission::query()
                ->orderBy('name')
                ->pluck('name'),

            'coreRoles' => self::CORE_ROLES,
        ]);
    }

    public function store(
        Request $request
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('roles.manage'),
            403
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:roles,name',
            ],
        ]);

        Role::create([
            'name' => Str::of($validated['name'])
                ->squish()
                ->toString(),
            'guard_name' => 'web',
        ]);

        activity('permissions')
            ->causedBy($request->user())
            ->event('role-created')
            ->withProperties($validated)
            ->log('Role created');

        return back()->with(
            'success',
            'Role created.'
        );
    }

    public function update(
        Request $request,
        Role $role
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('roles.manage'),
            403
        );

        $validated = $request->validate([
            'permissions' => [
                'array',
            ],
            'permissions.*' => [
                'string',
                'exists:permissions,name',
            ],
        ]);

        // Director and Admin always retain all permissions.
        if (
            in_array(
                $role->name,
                ['Director', 'Admin'],
                true
            )
        ) {
            $role->syncPermissions(
                Permission::all()
            );
        } else {
            $role->syncPermissions(
                $validated['permissions'] ?? []
            );
        }

        activity('permissions')
            ->causedBy($request->user())
            ->event('permissions-updated')
            ->withProperties([
                'role' => $role->name,
                'permissions' => $role
                    ->permissions
                    ->pluck('name'),
            ])
            ->log('Role permissions updated');

        return back()->with(
            'success',
            'Role permissions updated.'
        );
    }

    public function destroy(
        Request $request,
        Role $role
    ): RedirectResponse {
        abort_unless(
            $request->user()->can('roles.manage'),
            403
        );

        if (
            in_array(
                $role->name,
                self::CORE_ROLES,
                true
            )
        ) {
            return back()->with(
                'error',
                'Core system roles cannot be deleted.'
            );
        }

        if ($role->users()->exists()) {
            return back()->with(
                'error',
                'Remove users from this role before deleting it.'
            );
        }

        $name = $role->name;

        $role->delete();

        activity('permissions')
            ->causedBy($request->user())
            ->event('role-deleted')
            ->withProperties([
                'role' => $name,
            ])
            ->log('Role deleted');

        return back()->with(
            'success',
            'Role deleted.'
        );
    }
}
