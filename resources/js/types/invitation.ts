import { WorkspaceRole } from './workspace';

export enum InvitationStatus {
    PENDING = 'pending',
    ACCEPTED = 'accepted',
    CANCELLED = 'cancelled',
    EXPIRED = 'expired',
}

export interface Invitation {
    id: string;
    workspaceId: string;
    email: string;
    role: WorkspaceRole;
    token: string;
    status: InvitationStatus;
    invitedBy: string;
    createdAt: string;
    expiresAt: string | null;
    acceptedAt: string | null;
}

export interface InviteMemberRequest {
    workspaceId: string;
    email: string;
    role: WorkspaceRole;
}

export interface InviteMemberResponse {
    invitation: Invitation;
}

export interface AcceptInvitationRequest {
    token: string;
}
