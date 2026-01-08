import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { WorkspaceSwitcher } from '@/components/workspace-switcher';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { LayoutGrid } from 'lucide-react';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
];

type AppSidebarProps = {
    workspaces?: Array<{
        id: string;
        name: string;
        slug: string;
        avatar?: string | null;
        plan: string;
    }>;
    currentWorkspace?: {
        id: string;
        name: string;
        slug: string;
        avatar?: string | null;
        plan: string;
    };
};

export function AppSidebar({ workspaces = [], currentWorkspace }: AppSidebarProps) {
    const { version } = usePage<SharedData>().props;

    return (
        <Sidebar variant="sidebar">
            <SidebarHeader>
                {currentWorkspace && (
                    <WorkspaceSwitcher workspaces={workspaces} currentWorkspace={currentWorkspace} />
                )}
            </SidebarHeader>

            <SidebarContent className={'gap-6'}>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
                <div className="px-3 py-2 text-center">
                    <span className="text-xs text-muted-foreground">v{version}</span>
                </div>
            </SidebarFooter>
        </Sidebar>
    );
}
