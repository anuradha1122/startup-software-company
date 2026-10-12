<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)
            ->forgetCachedPermissions();

        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.export',

            'roles.manage',

            'company-settings.view',
            'company-settings.update',

            'audit.view',
            'audit.export',

            'leads.view',
            'leads.create',
            'leads.update',
            'leads.delete',
            'leads.assign',
            'leads.convert',
            'leads.export',

            'clients.view',
            'clients.create',
            'clients.update',
            'clients.delete',
            'clients.export',

            'clients.contacts.manage',
            'clients.billing.manage',
            'clients.documents.manage',
            'clients.documents.download',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $roles = [
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

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        $allPermissions = Permission::all();

        Role::findByName('Sales')
            ->syncPermissions([
                'leads.view',
                'leads.create',
                'leads.update',
                'leads.assign',
                'leads.convert',
                'leads.export',
            ]);

        Role::findByName('Project Manager')
            ->givePermissionTo([
                'leads.view',
                'leads.export',
                'clients.view',
                'clients.documents.download',
            ]);

        Role::findByName('Sales')
            ->givePermissionTo([
                'clients.view',
                'clients.create',
                'clients.update',
                'clients.export',
                'clients.contacts.manage',
                'clients.documents.manage',
                'clients.documents.download',
            ]);

        Role::findByName('Finance')
            ->givePermissionTo([
                'clients.view',
                'clients.export',
                'clients.billing.manage',
                'clients.documents.download',
            ]);

        Role::findByName('Director')
            ->syncPermissions($allPermissions);

        Role::findByName('Admin')
            ->syncPermissions($allPermissions);
    }
}
