import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/react';
import { type PropsWithChildren } from 'react';

type Workspace = {
    id: string;
    name: string;
    slug: string;
    plan: string;
};

type AppSidebarLayoutProps = PropsWithChildren<{
    breadcrumbs?: BreadcrumbItem[];
}>;

export default function AppSidebarLayout({ children, breadcrumbs = [] }: AppSidebarLayoutProps) {
    const { props } = usePage<{
        workspaces?: Workspace[];
        currentWorkspace?: Workspace;
    }>();

    return (
        <AppShell variant="sidebar">
            <AppSidebar workspaces={props.workspaces} currentWorkspace={props.currentWorkspace} />
            <AppContent variant="sidebar">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
