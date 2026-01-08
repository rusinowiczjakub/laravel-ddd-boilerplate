import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { WorkspaceRole } from '@/types/workspace';

interface InviteEntry {
    email: string;
    role: WorkspaceRole;
}

interface UseInviteTeamReturn {
    invites: InviteEntry[];
    addInvite: () => void;
    removeInvite: (index: number) => void;
    updateEmail: (index: number, value: string) => void;
    updateRole: (index: number, role: WorkspaceRole) => void;
    finishOnboarding: () => void;
    skip: () => void;
    isLoading: boolean;
}

export const useInviteTeam = (workspaceName: string, plan: string): UseInviteTeamReturn => {
    const [invites, setInvites] = useState<InviteEntry[]>([
        { email: '', role: WorkspaceRole.COLLABORATOR }
    ]);
    const { data, post, processing } = useForm({
        name: workspaceName,
        plan: plan,
        invitations: [] as InviteEntry[],
    });

    const addInvite = () => {
        if (invites.length < 5) {
            setInvites([...invites, { email: '', role: WorkspaceRole.COLLABORATOR }]);
        }
    };

    const removeInvite = (index: number) => {
        setInvites(invites.filter((_, i) => i !== index));
    };

    const updateEmail = (index: number, value: string) => {
        const newInvites = [...invites];
        newInvites[index].email = value;
        setInvites(newInvites);
    };

    const updateRole = (index: number, role: WorkspaceRole) => {
        const newInvites = [...invites];
        newInvites[index].role = role;
        setInvites(newInvites);
    };

    const finishOnboarding = () => {
        const validInvites = invites.filter(inv => inv.email.trim() && inv.email.includes('@'));

        // Update form data with valid invitations
        data.invitations = validInvites;

        post('/workspaces');
    };

    const skip = () => {
        // Clear invitations
        data.invitations = [];

        post('/workspaces');
    };

    return {
        invites,
        addInvite,
        removeInvite,
        updateEmail,
        updateRole,
        finishOnboarding,
        skip,
        isLoading: processing,
    };
};
