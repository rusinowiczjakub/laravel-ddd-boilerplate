import { router, usePage } from '@inertiajs/react';
import { useState, useCallback } from 'react';
import { type SharedData } from '@/types';
import { type WorkspaceMemberWithUser, WorkspaceRole } from '@/types/workspace';
import { useConfirmation } from '@/hooks/use-confirmation';

interface UseWorkspaceMembersProps {
    members: WorkspaceMemberWithUser[];
}

interface UseWorkspaceMembersReturn {
    members: WorkspaceMemberWithUser[];
    isInviteModalOpen: boolean;
    openInviteModal: () => void;
    closeInviteModal: () => void;
    isInviting: boolean;
    inviteEmail: string;
    inviteRole: WorkspaceRole;
    setInviteEmail: (email: string) => void;
    setInviteRole: (role: WorkspaceRole) => void;
    submitInvite: () => void;
    removeMember: (memberId: string) => void;
    changeRole: (memberId: string, role: WorkspaceRole) => void;
    leaveMember: (memberId: string) => void;
    cancelInvitation: (invitationId: string) => void;
    isTransferModalOpen: boolean;
    openTransferModal: () => void;
    closeTransferModal: () => void;
    transferOwnership: (newOwnerId: string) => void;
    isTransferring: boolean;
}

export const useWorkspaceMembers = ({ members }: UseWorkspaceMembersProps): UseWorkspaceMembersReturn => {
    const { currentWorkspace, auth } = usePage<SharedData>().props;
    const { confirm } = useConfirmation();
    const [isInviteModalOpen, setIsInviteModalOpen] = useState(false);
    const [isInviting, setIsInviting] = useState(false);
    const [inviteEmail, setInviteEmail] = useState('');
    const [inviteRole, setInviteRole] = useState<WorkspaceRole>(WorkspaceRole.COLLABORATOR);
    const [isTransferModalOpen, setIsTransferModalOpen] = useState(false);
    const [isTransferring, setIsTransferring] = useState(false);

    const openInviteModal = useCallback(() => {
        setIsInviteModalOpen(true);
    }, []);

    const closeInviteModal = useCallback(() => {
        setIsInviteModalOpen(false);
        setInviteEmail('');
        setInviteRole(WorkspaceRole.COLLABORATOR);
    }, []);

    const submitInvite = useCallback(() => {
        if (!currentWorkspace || !inviteEmail) return;

        setIsInviting(true);
        router.post(
            `/workspaces/${currentWorkspace.id}/invite`,
            {
                invitations: [{ email: inviteEmail, role: inviteRole }],
                redirect: 'settings.workspace',
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeInviteModal();
                },
                onFinish: () => {
                    setIsInviting(false);
                },
            }
        );
    }, [currentWorkspace, inviteEmail, inviteRole, closeInviteModal]);

    const removeMember = useCallback(async (memberId: string) => {
        if (!currentWorkspace) return;

        const confirmed = await confirm({
            title: 'Remove Member',
            description: 'Are you sure you want to remove this member from the workspace?',
            confirmText: 'Remove',
            variant: 'destructive',
        });
        if (!confirmed) return;

        router.delete(`/workspaces/${currentWorkspace.id}/members/${memberId}`, {
            preserveScroll: true,
        });
    }, [currentWorkspace, confirm]);

    const changeRole = useCallback((memberId: string, role: WorkspaceRole) => {
        if (!currentWorkspace) return;

        router.patch(
            `/workspaces/${currentWorkspace.id}/members/${memberId}`,
            { role },
            { preserveScroll: true }
        );
    }, [currentWorkspace]);

    const leaveMember = useCallback(async (memberId: string) => {
        if (!currentWorkspace || !auth.user) return;

        const member = members.find(m => m.id === memberId);
        if (!member) return;

        // If current user is owner, open transfer modal
        if (member.isOwner) {
            setIsTransferModalOpen(true);
            return;
        }

        // Otherwise, just leave
        const confirmed = await confirm({
            title: 'Leave Workspace',
            description: 'Are you sure you want to leave this workspace?',
            confirmText: 'Leave',
            variant: 'destructive',
        });
        if (!confirmed) return;

        router.delete(`/workspaces/${currentWorkspace.id}/members/${memberId}`, {
            preserveScroll: true,
        });
    }, [currentWorkspace, auth.user, members, confirm]);

    const cancelInvitation = useCallback(async (invitationId: string) => {
        if (!currentWorkspace) return;

        const confirmed = await confirm({
            title: 'Cancel Invitation',
            description: 'Are you sure you want to cancel this invitation?',
            confirmText: 'Cancel Invitation',
            variant: 'destructive',
        });
        if (!confirmed) return;

        router.delete(`/workspaces/${currentWorkspace.id}/invitations/${invitationId}`, {
            preserveScroll: true,
        });
    }, [currentWorkspace, confirm]);

    const openTransferModal = useCallback(() => {
        setIsTransferModalOpen(true);
    }, []);

    const closeTransferModal = useCallback(() => {
        setIsTransferModalOpen(false);
    }, []);

    const transferOwnership = useCallback((newOwnerId: string) => {
        if (!currentWorkspace) return;

        setIsTransferring(true);
        router.post(
            `/workspaces/${currentWorkspace.id}/transfer`,
            { new_owner_id: newOwnerId },
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeTransferModal();
                },
                onFinish: () => {
                    setIsTransferring(false);
                },
            }
        );
    }, [currentWorkspace, closeTransferModal]);

    return {
        members,
        isInviteModalOpen,
        openInviteModal,
        closeInviteModal,
        isInviting,
        inviteEmail,
        inviteRole,
        setInviteEmail,
        setInviteRole,
        submitInvite,
        removeMember,
        changeRole,
        leaveMember,
        cancelInvitation,
        isTransferModalOpen,
        openTransferModal,
        closeTransferModal,
        transferOwnership,
        isTransferring,
    };
};
