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
    FileSignature,
    ReceiptText,
    ScrollText,
    BriefcaseBusiness,
    ListTodo,
    UsersRound,
    Clock3,
    Truck,
    Banknote,
    FileText,
    WalletCards,
    ChartNoAxesCombined,
    Activity,
    FileChartColumn,
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

    if (can('admin-operations.view')) {
        administrationNavItems.push({
            title: 'Operations',
            href: '/admin/operations',
            icon: Activity,
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

    const commercialNavItems: NavItem[] = [];

    if (can('proposals.view')) {
        commercialNavItems.push({
            title: 'Proposals',
            href: '/admin/proposals',
            icon: FileSignature,
        });
    }

    if (can('quotations.view')) {
        commercialNavItems.push({
            title: 'Quotations',
            href: '/admin/quotations',
            icon: ReceiptText,
        });
    }

    if (can('contracts.view')) {
        commercialNavItems.push({
            title: 'Contracts',
            href: '/admin/contracts',
            icon: ScrollText,
        });
    }

    const projectNavItems: NavItem[] = [];

    if (can('projects.view')) {
        projectNavItems.push({
            title: 'Projects',
            href: '/admin/projects',
            icon: BriefcaseBusiness,
        });
    }

    if (can('tasks.view')) {
        projectNavItems.push({
            title: 'Task Workflow',
            href: '/admin/projects',
            icon: ListTodo,
        });
    }

    const peopleNavItems: NavItem[] = [];

    if (can('people.view')) {
        peopleNavItems.push({
            title: 'People',
            href: '/admin/people',
            icon: UsersRound,
        });
    }

    const workNavItems: NavItem[] = [];

    if (can('timesheets.view-own') || can('timesheets.view-all')) {
        workNavItems.push({
            title: 'Timesheets',
            href: '/admin/timesheets',
            icon: Clock3,
        });
    }

    const financeOpsNavItems: NavItem[] = [];

    if (can('suppliers.view')) {
        financeOpsNavItems.push({
            title: 'Suppliers',
            href: '/admin/suppliers',
            icon: Truck,
        });
    }

    if (can('expenses.view-own') || can('expenses.view-all')) {
        financeOpsNavItems.push({
            title: 'Project Expenses',
            href: '/admin/projects',
            icon: ReceiptText,
        });
    }

    const billingNavItems: NavItem[] = [];

    if (can('invoices.view')) {
        billingNavItems.push({
            title: 'Invoices',
            href: '/admin/invoices',
            icon: FileText,
        });
    }

    if (can('payments.view')) {
        billingNavItems.push({
            title: 'Payments',
            href: '/admin/payments',
            icon: WalletCards,
        });
    }

    if (can('receivables.view')) {
        billingNavItems.push({
            title: 'Receivables',
            href: '/admin/receivables',
            icon: Banknote,
        });

        if (can('finance.dashboard.view')) {
            billingNavItems.push({
                title: 'Finance',
                href: '/admin/finance',
                icon: ChartNoAxesCombined,
            });
        }
    }

    if (can('reports.view')) {
        billingNavItems.push({
            title: 'Reports',
            href: '/admin/reports',
            icon: FileChartColumn,
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

                {commercialNavItems.length > 0 && (
                    <NavMain items={commercialNavItems} label="Commercial" />
                )}

                {projectNavItems.length > 0 && (
                    <NavMain items={projectNavItems} label="Projects" />
                )}

                {peopleNavItems.length > 0 && (
                    <NavMain items={peopleNavItems} label="People" />
                )}

                {workNavItems.length > 0 && (
                    <NavMain items={workNavItems} label="Work" />
                )}

                {financeOpsNavItems.length > 0 && (
                    <NavMain items={financeOpsNavItems} label="Costs" />
                )}

                {billingNavItems.length > 0 && (
                    <NavMain items={billingNavItems} label="Billing" />
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
