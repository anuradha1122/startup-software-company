import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';

export function NavMain({
    items,
    label,
}: {
    items: NavItem[];
    label?: string;
}) {
    const page = usePage();

    return (
        <SidebarGroup>
            {label && <SidebarGroupLabel>{label}</SidebarGroupLabel>}

            <SidebarMenu>
                {items.map((item) => {
                    const href =
                        typeof item.href === 'string'
                            ? item.href
                            : item.href.url;

                    const isActive =
                        page.url === href || page.url.startsWith(`${href}/`);

                    return (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton
                                asChild
                                isActive={isActive}
                                tooltip={{
                                    children: item.title,
                                }}
                            >
                                <Link href={item.href} prefetch>
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    );
                })}
            </SidebarMenu>
        </SidebarGroup>
    );
}
