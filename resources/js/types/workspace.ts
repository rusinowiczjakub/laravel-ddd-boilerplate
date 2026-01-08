export enum WorkspacePlan {
    FREE = 'free',
    STARTER = 'starter',
    PRO = 'pro',
    ENTERPRISE = 'enterprise',
}

export enum WorkspaceStatus {
    ACTIVE = 'active',
    SUSPENDED = 'suspended',
    CANCELLED = 'cancelled',
}

export enum WorkspaceRole {
    ADMINISTRATOR = 'administrator',
    COLLABORATOR = 'collaborator',
}

export interface Workspace {
    id: string;
    name: string;
    slug: string;
    avatar: string | null;
    plan: WorkspacePlan;
    status: WorkspaceStatus;
    ownerId: string;
    createdAt: string;
}

export interface WorkspaceMember {
    id: string;
    workspaceId: string;
    userId: string;
    role: WorkspaceRole;
    addedAt: string;
}

export type MemberStatus = 'active' | 'pending';

/** Member with user data for display purposes */
export interface WorkspaceMemberWithUser {
    id: string;
    userId: string | null;
    name: string | null;
    email: string;
    role: WorkspaceRole;
    addedAt: string;
    isOwner: boolean;
    status: MemberStatus;
    invitationId: string | null;
}

export interface WorkspaceApiKey {
    id: string;
    workspaceId: string;
    name: string;
    keyPrefix: string;
    isTest: boolean;
    lastUsedAt: string | null;
    expiresAt: string | null;
    createdAt: string;
}

export interface CreateWorkspaceRequest {
    name: string;
    plan?: WorkspacePlan;
}

export interface CreateWorkspaceResponse {
    workspace: Workspace;
    member: WorkspaceMember;
}
