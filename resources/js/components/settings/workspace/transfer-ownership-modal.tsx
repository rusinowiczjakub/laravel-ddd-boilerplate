import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { type WorkspaceMemberWithUser } from '@/types/workspace';
import { AlertTriangle, Loader2 } from 'lucide-react';

interface TransferOwnershipModalProps {
    isOpen: boolean;
    onClose: () => void;
    members: WorkspaceMemberWithUser[];
    currentUserId?: string;
    onTransfer: (newOwnerId: string) => void;
    isTransferring: boolean;
}

export const TransferOwnershipModal = ({
    isOpen,
    onClose,
    members,
    currentUserId,
    onTransfer,
    isTransferring,
}: TransferOwnershipModalProps) => {
    const [selectedMemberId, setSelectedMemberId] = useState<string>('');

    // Filter out the current owner from the list
    const eligibleMembers = members.filter(
        (m) => m.userId !== currentUserId && !m.isOwner
    );

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (selectedMemberId) {
            const member = members.find(m => m.id === selectedMemberId);
            if (member) {
                onTransfer(member.userId);
            }
        }
    };

    const handleOpenChange = (open: boolean) => {
        if (!open) {
            setSelectedMemberId('');
            onClose();
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={handleOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <AlertTriangle className="size-5 text-amber-500" />
                        Transfer ownership & leave
                    </DialogTitle>
                    <DialogDescription>
                        Before you can leave this workspace, you must transfer ownership to another
                        member. The new owner will have full control over the workspace.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="space-y-2">
                        <Label htmlFor="new-owner">New owner</Label>
                        {eligibleMembers.length > 0 ? (
                            <Select value={selectedMemberId} onValueChange={setSelectedMemberId}>
                                <SelectTrigger>
                                    <SelectValue placeholder="Select a member" />
                                </SelectTrigger>
                                <SelectContent>
                                    {eligibleMembers.map((member) => (
                                        <SelectItem key={member.id} value={member.id}>
                                            {member.name} ({member.email})
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        ) : (
                            <p className="text-sm text-muted-foreground">
                                No other members available. Invite someone to the workspace first,
                                or delete the workspace instead.
                            </p>
                        )}
                    </div>

                    <div className="rounded-md bg-amber-50 dark:bg-amber-950 p-3 text-sm text-amber-800 dark:text-amber-200">
                        <strong>Warning:</strong> This action cannot be undone. You will become a
                        regular member and will no longer be able to delete the workspace or
                        transfer ownership.
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={onClose}>
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            variant="destructive"
                            disabled={isTransferring || !selectedMemberId || eligibleMembers.length === 0}
                        >
                            {isTransferring && <Loader2 className="mr-2 size-4 animate-spin" />}
                            Transfer & leave
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
};
