import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedWorkspace {
    id: string;
    name: string;
    slug: string;
    plan: string;
    ownerId: string;
    subscriptionStatus?: 'active' | 'trialing' | 'past_due' | 'canceled' | null;
    subscriptionEndsAt?: string | null;
    subscriptionCurrentPeriodEnd?: string | null;
}

export interface WorkspaceSubscription {
    pendingPlan?: string | null;
    pendingBillingPeriod?: string | null;
    planChangesAt?: string | null;
    subscriptionStatus?: 'active' | 'trialing' | 'past_due' | 'canceled' | null;
    subscriptionEndsAt?: string | null;
    subscriptionCurrentPeriodEnd?: string | null;
}

export interface SharedData {
    name: string;
    version: string;
    quote: { message: string; author: string };
    auth: Auth;
    workspaces: SharedWorkspace[];
    currentWorkspace: SharedWorkspace | null;
    currentWorkspaceSubscription?: WorkspaceSubscription | null;
    waitlistMode: boolean;
    ziggy: Config & { location: string };
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    first_name?: string;
    last_name?: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_confirmed_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}
