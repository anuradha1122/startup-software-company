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

            'requirements.view',
            'requirements.create',
            'requirements.update',
            'requirements.review',
            'requirements.request-approval',
            'requirements.record-approval',
            'requirements.export',
            'requirements.attachments.manage',
            'requirements.attachments.download',

            'estimates.view',
            'estimates.create',
            'estimates.update',
            'estimates.approve',
            'estimates.export',

            'proposals.view',
            'proposals.create',
            'proposals.update',
            'proposals.approve',
            'proposals.send',
            'proposals.record-decision',
            'proposals.export',

            'quotations.view',
            'quotations.create',
            'quotations.update',
            'quotations.approve',
            'quotations.send',
            'quotations.record-decision',
            'quotations.export',

            'contracts.view',
            'contracts.create',
            'contracts.update',
            'contracts.issue',
            'contracts.mark-signed',
            'contracts.export',
            'contracts.documents.manage',
            'contracts.documents.download',

            'projects.view',
            'projects.create',
            'projects.update',
            'projects.activate',
            'projects.complete',
            'projects.export',

            'projects.charter.manage',
            'projects.charter.approve',

            'projects.phases.manage',
            'projects.milestones.manage',
            'projects.stakeholders.manage',

            'projects.risks.manage',
            'projects.risks.escalate',

            'projects.decisions.manage',
            'projects.kickoff.send',
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
                'requirements.view',
                'requirements.create',
                'requirements.update',
                'requirements.review',
                'requirements.request-approval',
                'requirements.record-approval',
                'requirements.export',
                'requirements.attachments.manage',
                'requirements.attachments.download',

                'estimates.view',
                'estimates.create',
                'estimates.update',
                'estimates.export',

                'proposals.view',
                'proposals.create',
                'proposals.update',
                'proposals.export',

                'quotations.view',
                'quotations.create',
                'quotations.update',
                'quotations.export',

                'contracts.view',
                'contracts.documents.download',

                'projects.view',
                'projects.create',
                'projects.update',
                'projects.activate',
                'projects.export',

                'projects.charter.manage',

                'projects.phases.manage',
                'projects.milestones.manage',
                'projects.stakeholders.manage',

                'projects.risks.manage',
                'projects.risks.escalate',

                'projects.decisions.manage',
                'projects.kickoff.send',
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
                'requirements.view',
                'requirements.export',
                'estimates.view',

                'proposals.view',
                'proposals.create',
                'proposals.update',
                'proposals.send',
                'proposals.record-decision',
                'proposals.export',

                'quotations.view',
                'quotations.create',
                'quotations.update',
                'quotations.send',
                'quotations.record-decision',
                'quotations.export',

                'contracts.view',
                'contracts.documents.download',

                'projects.view',
                'projects.export',
            ]);

        Role::findByName('Finance')
            ->givePermissionTo([
                'clients.view',
                'clients.export',
                'clients.billing.manage',
                'clients.documents.download',

                'quotations.view',
                'quotations.export',

                'contracts.view',
                'contracts.export',
                'contracts.documents.download',

                'projects.view',
                'projects.export',
            ]);

        Role::findByName('Director')
            ->syncPermissions($allPermissions);

        Role::findByName('Admin')
            ->syncPermissions($allPermissions);
    }
}
