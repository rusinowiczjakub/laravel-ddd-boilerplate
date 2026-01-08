import { ChevronsUpDown, Plus } from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { router } from '@inertiajs/react';
import { WorkspaceAvatar } from '@/components/workspace-avatar';
import { SubscriptionStatusBadge } from '@/components/subscription-status-badge';

type Workspace = {
    id: string;
    name: string;
    slug: string;
    avatar?: string | null;
    plan: string;
    pendingPlan?: string | null;
    planChangesAt?: string | null;
    subscriptionStatus?: 'active' | 'trialing' | 'past_due' | 'canceled' | null;
    subscriptionEndsAt?: string | null;
    subscriptionCurrentPeriodEnd?: string | null;
};

type WorkspaceSwitcherProps = {
    workspaces: Workspace[];
    currentWorkspace: Workspace;
};

export function WorkspaceSwitcher({ workspaces, currentWorkspace }: WorkspaceSwitcherProps) {
    const { isMobile } = useSidebar();

    const handleWorkspaceChange = (workspaceId: string) => {
        router.post(`/workspaces/${workspaceId}/switch`);
    };

    const formatNextBillingDate = (dateString: string | null | undefined) => {
        if (!dateString) return null;
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    };

    return (
        <SidebarMenu>
            <SidebarMenuItem>
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <SidebarMenuButton
                            size="lg"
                            className="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                        >
                            <WorkspaceAvatar
                                name={currentWorkspace.name}
                                avatar={currentWorkspace.avatar}
                                size="md"
                            />
                            <div className="flex flex-1 flex-col text-left text-sm leading-tight">
                                <span className="truncate font-semibold mb-2">
                                    {currentWorkspace.name}
                                    <SubscriptionStatusBadge
                                        status={currentWorkspace.subscriptionStatus}
                                        plan={currentWorkspace.plan}
                                        endsAt={currentWorkspace.subscriptionEndsAt}
                                        className="text-[10px] px-1.5 py-0.5 gap-1 w-fit ml-2"
                                    />
                                </span>
                                {currentWorkspace.subscriptionStatus === 'active' && (
                                    <span className="text-[10px] text-muted-foreground">
                                        {currentWorkspace.pendingPlan && currentWorkspace.planChangesAt
                                            ? `→ ${currentWorkspace.pendingPlan.charAt(0).toUpperCase() + currentWorkspace.pendingPlan.slice(1)} on ${formatNextBillingDate(currentWorkspace.planChangesAt)}`
                                            : currentWorkspace.subscriptionEndsAt
                                                ? null // Badge already shows "Cancels [date]"
                                                : currentWorkspace.subscriptionCurrentPeriodEnd
                                                    ? `Next billing: ${formatNextBillingDate(currentWorkspace.subscriptionCurrentPeriodEnd)}`
                                                    : null
                                        }
                                    </span>
                                )}

                            </div>
                            <ChevronsUpDown className="ml-auto" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        className="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                        align="start"
                        side={isMobile ? 'bottom' : 'right'}
                        sideOffset={4}
                    >
                        <DropdownMenuLabel className="text-muted-foreground text-xs">Workspaces</DropdownMenuLabel>
                        {workspaces.map((workspace) => (
                            <DropdownMenuItem
                                key={workspace.id}
                                onClick={() => handleWorkspaceChange(workspace.id)}
                                className="gap-2 p-2"
                            >
                                <WorkspaceAvatar name={workspace.name} avatar={workspace.avatar} size="sm" />
                                <div className="flex flex-col gap-0.5">
                                    <div className="flex items-center gap-2">
                                        <span className="font-medium">{workspace.name}</span>
                                        <SubscriptionStatusBadge
                                            status={workspace.subscriptionStatus}
                                            plan={workspace.plan}
                                            endsAt={workspace.subscriptionEndsAt}
                                            className="text-[10px] px-1.5 py-0 gap-1"
                                        />
                                    </div>
                                    {workspace.subscriptionStatus === 'active' && (
                                        <span className="text-[10px] text-muted-foreground">
                                            {workspace.pendingPlan && workspace.planChangesAt
                                                ? `→ ${workspace.pendingPlan.charAt(0).toUpperCase() + workspace.pendingPlan.slice(1)} on ${formatNextBillingDate(workspace.planChangesAt)}`
                                                : workspace.subscriptionEndsAt
                                                    ? null
                                                    : workspace.subscriptionCurrentPeriodEnd
                                                        ? `Next billing: ${formatNextBillingDate(workspace.subscriptionCurrentPeriodEnd)}`
                                                        : null
                                            }
                                        </span>
                                    )}
                                </div>
                            </DropdownMenuItem>
                        ))}
                        <DropdownMenuSeparator />
                        <DropdownMenuItem
                            className="gap-2 p-2"
                            onClick={() => router.visit('/onboarding/create-workspace')}
                        >
                            <div className="bg-background flex size-6 items-center justify-center rounded-md border">
                                <Plus className="size-4" />
                            </div>
                            <div className="font-medium">Add workspace</div>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>
        </SidebarMenu>
    );
}
