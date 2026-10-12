import { Link, usePage } from '@inertiajs/react';
import {
    Bell,
    Building2,
    ClipboardList,
    LayoutGrid,
    ShieldCheck,
    Users,
    ContactRound,
    ClipboardCheck,
} from 'lucide-react';

import AppLogo from '@/components/app-logo';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

export function AppSidebar() {
    const page = usePage<any>();

    const permissions: string[] = page.props.auth?.permissions ?? [];

    const can = (permission: string) => permissions.includes(permission);

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    const administrationNavItems: NavItem[] = [];

    if (can('users.view')) {
        administrationNavItems.push({
            title: 'Users',
            href: '/admin/users',
            icon: Users,
        });
    }

    if (can('roles.manage')) {
        administrationNavItems.push({
            title: 'Roles & Permissions',
            href: '/admin/roles',
            icon: ShieldCheck,
        });
    }

    if (can('company-settings.view')) {
        administrationNavItems.push({
            title: 'Company Settings',
            href: '/admin/company',
            icon: Building2,
        });
    }

    if (can('audit.view')) {
        administrationNavItems.push({
            title: 'Audit Log',
            href: '/admin/audit',
            icon: ClipboardList,
        });
    }

    const accountNavItems: NavItem[] = [
        {
            title: 'Notifications',
            href: '/notifications',
            icon: Bell,
        },
    ];

    const crmNavItems: NavItem[] = [];

    if (can('leads.view')) {
        crmNavItems.push({
            title: 'Leads',
            href: '/admin/leads',
            icon: ContactRound,
        });
    }

    if (can('clients.view')) {
        crmNavItems.push({
            title: 'Clients',
            href: '/admin/clients',
            icon: Building2,
        });
    }

    if (can('requirements.view')) {
        crmNavItems.push({
            title: 'Requirements',
            href: '/admin/requirements',
            icon: ClipboardCheck,
        });
    }

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} label="Platform" />

                {crmNavItems.length > 0 && (
                    <NavMain items={crmNavItems} label="CRM" />
                )}

                {administrationNavItems.length > 0 && (
                    <NavMain
                        items={administrationNavItems}
                        label="Administration"
                    />
                )}

                <NavMain items={accountNavItems} label="Account" />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
