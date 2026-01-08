import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { WorkspaceRole } from '@/types/workspace';
import { Loader2 } from 'lucide-react';

interface InviteMemberModalProps {
    isOpen: boolean;
    onClose: () => void;
    email: string;
    onEmailChange: (email: string) => void;
    role: WorkspaceRole;
    onRoleChange: (role: WorkspaceRole) => void;
    onSubmit: () => void;
    isSubmitting: boolean;
}

export const InviteMemberModal = ({
    isOpen,
    onClose,
    email,
    onEmailChange,
    role,
    onRoleChange,
    onSubmit,
    isSubmitting,
}: InviteMemberModalProps) => {
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSubmit();
    };

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Invite team member</DialogTitle>
                    <DialogDescription>
                        Send an invitation to join your workspace. They will receive an email with
                        a link to accept the invitation.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="space-y-2">
                        <Label htmlFor="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            value={email}
                            onChange={(e) => onEmailChange(e.target.value)}
                            placeholder="colleague@company.com"
                            autoComplete="email"
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="role">Role</Label>
                        <Select value={role} onValueChange={(value) => onRoleChange(value as WorkspaceRole)}>
                            <SelectTrigger>
                                <SelectValue placeholder="Select a role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value={WorkspaceRole.ADMINISTRATOR}>
                                    Admin - Can manage members and settings
                                </SelectItem>
                                <SelectItem value={WorkspaceRole.COLLABORATOR}>
                                    Member - Can view and use workspace features
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={onClose}>
                            Cancel
                        </Button>
                        <Button type="submit" disabled={isSubmitting || !email}>
                            {isSubmitting && <Loader2 className="mr-2 size-4 animate-spin" />}
                            Send invitation
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
};
