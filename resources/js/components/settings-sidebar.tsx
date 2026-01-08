import {UserAvatar} from '@/components/user-avatar';
import {WorkspaceSwitcher} from '@/components/workspace-switcher';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import {Link, usePage} from '@inertiajs/react';
import {ArrowLeft, CreditCard, Plus, Settings, TrendingUp, User} from 'lucide-react';

type SettingsSidebarProps = {
    workspaces?: Array<{
        id: string;
        name: string;
        slug: string;
        plan: string;
    }>;
    currentWorkspace?: {
        id: string;
        name: string;
        slug: string;
        plan: string;
    };
};

export function SettingsSidebar({workspaces = [], currentWorkspace}: SettingsSidebarProps) {
    const {url, props} = usePage<{
        auth: {
            user?: {
                name: string;
                first_name?: string;
                last_name?: string;
                email: string;
                avatar?: string;
            };
        };
    }>();

    const isActive = (path: string) => {
        return url.startsWith(path);
    };

    const user = props.auth?.user;

    return (
        <Sidebar variant="inset">
            <SidebarHeader>
                {/* Back button */}
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard">
                                <ArrowLeft className="size-4"/>
                                <span>Back</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                {/* User Info */}
                {user && (
                    <SidebarGroup>
                        <SidebarGroupContent>
                            <SidebarMenu>
                                <SidebarMenuItem>
                                    <SidebarMenuButton size="lg" className="pointer-events-none">
                                        <UserAvatar
                                            name={user.first_name && user.last_name
                                                ? `${user.first_name} ${user.last_name}`
                                                : user.name}
                                            avatar={user.avatar}
                                            size="md"
                                        />
                                        <div className="grid flex-1 text-left text-sm leading-tight">
                                            <span className="truncate font-semibold">
                                                {user.first_name && user.last_name
                                                    ? `${user.first_name} ${user.last_name}`
                                                    : user.name}
                                            </span>
                                        </div>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>
                )}

                {/* User Navigation */}
                <SidebarGroup>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            <SidebarMenuItem>
                                <SidebarMenuButton asChild isActive={isActive('/settings/profile')}>
                                    <Link href="/settings/profile">
                                        <User className="size-4"/>
                                        <span>Profile</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>

                {/* Workspace Switcher */}
                {currentWorkspace && (
                    <SidebarGroup>
                        <WorkspaceSwitcher workspaces={workspaces} currentWorkspace={currentWorkspace}/>
                    </SidebarGroup>
                )}

                {/* Workspace Settings */}
                <SidebarGroup>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            <SidebarMenuItem>
                                <SidebarMenuButton asChild isActive={isActive('/settings/workspace')}>
                                    <Link className={'gap-3'} href="/settings/workspace">
                                        <Settings className="size-4"/>
                                        <span>Settings</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                            <SidebarMenuItem>
                                <SidebarMenuButton asChild isActive={isActive('/settings/billing')}>
                                    <Link className={'gap-3'} href="/settings/billing">
                                        <CreditCard className="size-4"/>
                                        <span>Billing</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                            <SidebarMenuItem>
                                <SidebarMenuButton asChild isActive={isActive('/settings/usage')}>
                                    <Link className={'gap-3'} href="/settings/usage">
                                        <TrendingUp className="size-4"/>
                                        <span>Usage</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                            <SidebarMenuItem>
                                <SidebarMenuButton asChild>
                                    <Link className={'gap-3'} href="/onboarding/create-workspace">
                                        <div className={"flex aspect-square size-5 items-center justify-center rounded-md border border-neutral-300 bg-neutral-50 max-md:hidden dark:border-neutral-800 dark:bg-neutral-900"} variant="outline">
                                            <Plus className="size-4"/>
                                        </div>
                                        <span>Add Workspace</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>
            </SidebarContent>

            <SidebarFooter>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <div className="text-muted-foreground px-2 text-xs">
                            Signed in as
                            <br/>
                            <span className="font-medium">{user?.email}</span>
                        </div>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarFooter>
        </Sidebar>
    );
}
