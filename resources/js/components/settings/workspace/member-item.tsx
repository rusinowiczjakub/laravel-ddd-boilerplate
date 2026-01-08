import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { type WorkspaceMemberWithUser, WorkspaceRole } from '@/types/workspace';
import { ChevronDown, Clock, Crown, MoreVertical } from 'lucide-react';

interface MemberItemProps {
    member: WorkspaceMemberWithUser;
    onChangeRole: (role: WorkspaceRole) => void;
    onRemove: () => void;
    onLeave: () => void;
    onCancelInvitation?: () => void;
    isCurrentUser?: boolean;
    canManageMembers?: boolean;
}

export const MemberItem = ({
    member,
    onChangeRole,
    onRemove,
    onLeave,
    onCancelInvitation,
    isCurrentUser,
    canManageMembers,
}: MemberItemProps) => {
    const isPending = member.status === 'pending';

    const initials = member.name
        ? member.name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2)
        : member.email.slice(0, 2).toUpperCase();

    const roleLabel = member.role === WorkspaceRole.ADMINISTRATOR ? 'Admin' : 'Member';

    return (
        <div className={`flex items-center justify-between p-4 ${isPending ? 'opacity-70' : ''}`}>
            <div className="flex items-center gap-3">
                <div className={`flex size-8 items-center justify-center rounded-lg text-sm font-semibold ${
                    isPending
                        ? 'bg-muted text-muted-foreground border-2 border-dashed border-muted-foreground/30'
                        : 'bg-primary text-primary-foreground'
                }`}>
                    {initials}
                </div>
                <div>
                    <div className="flex items-center gap-2 font-medium">
                        {member.name || member.email}
                        {isCurrentUser && <span className="text-muted-foreground">(you)</span>}
                        {member.isOwner && (
                            <Badge variant="secondary" className="gap-1">
                                <Crown className="size-3" />
                                Owner
                            </Badge>
                        )}
                        {isPending && (
                            <Badge variant="outline" className="gap-1 text-amber-600 border-amber-600/30">
                                <Clock className="size-3" />
                                Pending
                            </Badge>
                        )}
                    </div>
                    {member.name && (
                        <div className="text-muted-foreground text-sm">{member.email}</div>
                    )}
                </div>
            </div>
            <div className="flex items-center gap-2">
                {/* Role dropdown for active non-owner members */}
                {!isPending && !member.isOwner && canManageMembers && (
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="outline" size="sm">
                                {roleLabel}
                                <ChevronDown className="ml-2 size-3" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem onClick={() => onChangeRole(WorkspaceRole.ADMINISTRATOR)}>
                                Admin
                            </DropdownMenuItem>
                            <DropdownMenuItem onClick={() => onChangeRole(WorkspaceRole.COLLABORATOR)}>
                                Member
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                )}

                {/* Role badge for pending invitations or when user can't manage */}
                {(isPending || (!member.isOwner && !canManageMembers)) && (
                    <Badge variant="outline">{roleLabel}</Badge>
                )}

                {/* Leave option for current user (active members only) */}
                {!isPending && isCurrentUser && (
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="sm">
                                <MoreVertical className="size-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem
                                onClick={onLeave}
                                className="text-destructive focus:text-destructive"
                            >
                                {member.isOwner ? 'Transfer ownership & leave' : 'Leave workspace'}
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                )}

                {/* Remove option for active non-owner members */}
                {!isPending && !isCurrentUser && !member.isOwner && canManageMembers && (
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="sm">
                                <MoreVertical className="size-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem
                                onClick={onRemove}
                                className="text-destructive focus:text-destructive"
                            >
                                Remove from workspace
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                )}

                {/* Cancel invitation option for pending invitations */}
                {isPending && canManageMembers && onCancelInvitation && (
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" size="sm">
                                <MoreVertical className="size-4" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem
                                onClick={onCancelInvitation}
                                className="text-destructive focus:text-destructive"
                            >
                                Cancel invitation
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                )}
            </div>
        </div>
    );
};
