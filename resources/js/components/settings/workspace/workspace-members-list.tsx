import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { type WorkspaceMemberWithUser, WorkspaceRole } from '@/types/workspace';
import { Plus } from 'lucide-react';
import { MemberItem } from './member-item';

interface WorkspaceMembersListProps {
    members: WorkspaceMemberWithUser[];
    currentUserId?: string;
    canManageMembers?: boolean;
    onInvite: () => void;
    onChangeRole: (memberId: string, role: WorkspaceRole) => void;
    onRemove: (memberId: string) => void;
    onLeave: (memberId: string) => void;
    onCancelInvitation: (invitationId: string) => void;
}

export const WorkspaceMembersList = ({
    members,
    currentUserId,
    canManageMembers,
    onInvite,
    onChangeRole,
    onRemove,
    onLeave,
    onCancelInvitation,
}: WorkspaceMembersListProps) => {
    const activeMembers = members.filter((m) => m.status === 'active');
    const pendingInvitations = members.filter((m) => m.status === 'pending');

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <h2 className="text-xl font-semibold">
                    {activeMembers.length} member{activeMembers.length !== 1 ? 's' : ''}
                    {pendingInvitations.length > 0 && (
                        <span className="text-muted-foreground font-normal ml-2">
                            ({pendingInvitations.length} pending)
                        </span>
                    )}
                </h2>
                {canManageMembers && (
                    <Button size="sm" onClick={onInvite}>
                        <Plus className="mr-2 size-4" />
                        Invite
                    </Button>
                )}
            </div>

            <Card>
                <CardContent className="divide-y p-0">
                    {members.map((member) => (
                        <MemberItem
                            key={member.id}
                            member={member}
                            isCurrentUser={member.userId === currentUserId}
                            canManageMembers={canManageMembers}
                            onChangeRole={(role) => onChangeRole(member.id, role)}
                            onRemove={() => onRemove(member.id)}
                            onLeave={() => onLeave(member.id)}
                            onCancelInvitation={
                                member.invitationId
                                    ? () => onCancelInvitation(member.invitationId!)
                                    : undefined
                            }
                        />
                    ))}
                    {members.length === 0 && (
                        <div className="text-muted-foreground p-8 text-center">
                            No members yet. Invite your team to get started.
                        </div>
                    )}
                </CardContent>
            </Card>
        </div>
    );
};
