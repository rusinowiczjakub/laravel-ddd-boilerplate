import {
    InviteMemberModal,
    TransferOwnershipModal,
    WorkspaceDangerZone,
    WorkspaceGeneralCard,
    WorkspaceMembersList,
} from '@/components/settings/workspace';
import { useWorkspaceMembers } from '@/hooks/use-workspace-members';
import { useWorkspaceSettings } from '@/hooks/use-workspace-settings';
import SettingsLayout from '@/layouts/settings-layout';
import { type SharedData } from '@/types';
import { type WorkspaceMemberWithUser, WorkspaceRole } from '@/types/workspace';
import { Head, usePage } from '@inertiajs/react';

interface WorkspaceSettingsProps {
    members: WorkspaceMemberWithUser[];
}

export default function WorkspaceSettings({ members = [] }: WorkspaceSettingsProps) {
    const { currentWorkspace, auth } = usePage<SharedData>().props;

    const {
        workspaceName,
        setWorkspaceName,
        handleNameBlur,
        enforce2FA,
        setEnforce2FA,
        isSaving,
        uploadAvatar,
        removeAvatar,
        isUploadingAvatar,
        avatar,
    } = useWorkspaceSettings();

    const {
        members: membersList,
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
        closeTransferModal,
        transferOwnership,
        isTransferring,
    } = useWorkspaceMembers({ members });

    // Determine if current user can manage members (is owner or admin)
    const currentUserMember = membersList.find(
        (m) => m.userId === auth.user?.id?.toString()
    );
    const isOwner = currentWorkspace?.ownerId === auth.user?.id?.toString();
    const isAdmin = currentUserMember?.role === WorkspaceRole.ADMINISTRATOR;
    const canManageMembers = isOwner || isAdmin;

    const handleLeaveWorkspace = () => {
        const myMember = membersList.find(m => m.userId === auth.user?.id?.toString());
        if (myMember) {
            leaveMember(myMember.id);
        }
    };

    const handleDeleteWorkspace = () => {
        // TODO: Implement delete workspace modal
        console.log('Delete workspace');
    };

    if (!currentWorkspace) {
        return (
            <SettingsLayout>
                <Head title="Workspace Settings" />
                <div className="text-muted-foreground py-8 text-center">
                    No workspace selected. Please create or select a workspace first.
                </div>
            </SettingsLayout>
        );
    }

    return (
        <SettingsLayout>
            <Head title={`${currentWorkspace.name} Settings`} />

            <div className="space-y-6">
                <div>
                    <h1 className="text-3xl font-bold">{currentWorkspace.name} Settings</h1>
                    <p className="text-muted-foreground">Update your workspace or invite new members</p>
                </div>

                <WorkspaceGeneralCard
                    workspaceName={workspaceName}
                    onNameChange={setWorkspaceName}
                    onNameBlur={handleNameBlur}
                    enforce2FA={enforce2FA}
                    onEnforce2FAChange={setEnforce2FA}
                    isSaving={isSaving}
                    avatar={avatar}
                    onAvatarUpload={uploadAvatar}
                    onAvatarRemove={removeAvatar}
                    isUploadingAvatar={isUploadingAvatar}
                />

                <WorkspaceMembersList
                    members={membersList}
                    currentUserId={auth.user?.id?.toString()}
                    canManageMembers={canManageMembers}
                    onInvite={openInviteModal}
                    onChangeRole={changeRole}
                    onRemove={removeMember}
                    onLeave={leaveMember}
                    onCancelInvitation={cancelInvitation}
                />

                <WorkspaceDangerZone
                    workspaceName={currentWorkspace.name}
                    isOwner={isOwner}
                    onLeave={handleLeaveWorkspace}
                    onDelete={handleDeleteWorkspace}
                />
            </div>

            <InviteMemberModal
                isOpen={isInviteModalOpen}
                onClose={closeInviteModal}
                email={inviteEmail}
                onEmailChange={setInviteEmail}
                role={inviteRole}
                onRoleChange={setInviteRole}
                onSubmit={submitInvite}
                isSubmitting={isInviting}
            />

            <TransferOwnershipModal
                isOpen={isTransferModalOpen}
                onClose={closeTransferModal}
                members={membersList}
                currentUserId={auth.user?.id?.toString()}
                onTransfer={transferOwnership}
                isTransferring={isTransferring}
            />
        </SettingsLayout>
    );
}
