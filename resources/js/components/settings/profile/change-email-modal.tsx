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
import { Loader2 } from 'lucide-react';

interface ChangeEmailModalProps {
    isOpen: boolean;
    onClose: () => void;
    password: string;
    onPasswordChange: (password: string) => void;
    newEmail: string;
    onNewEmailChange: (email: string) => void;
    onSubmit: () => void;
    isSubmitting: boolean;
    errors: {
        password?: string;
        email?: string;
    };
}

export const ChangeEmailModal = ({
    isOpen,
    onClose,
    password,
    onPasswordChange,
    newEmail,
    onNewEmailChange,
    onSubmit,
    isSubmitting,
    errors,
}: ChangeEmailModalProps) => {
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSubmit();
    };

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Change email address</DialogTitle>
                    <DialogDescription>
                        Enter your current password and new email address. You will need to verify
                        your new email before it becomes active.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="space-y-2">
                        <Label htmlFor="current-password">Current password</Label>
                        <Input
                            id="current-password"
                            type="password"
                            value={password}
                            onChange={(e) => onPasswordChange(e.target.value)}
                            placeholder="Enter your current password"
                            autoComplete="current-password"
                        />
                        {errors.password && (
                            <p className="text-sm text-red-500">{errors.password}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="new-email">New email address</Label>
                        <Input
                            id="new-email"
                            type="email"
                            value={newEmail}
                            onChange={(e) => onNewEmailChange(e.target.value)}
                            placeholder="Enter your new email"
                            autoComplete="email"
                        />
                        {errors.email && <p className="text-sm text-red-500">{errors.email}</p>}
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={onClose}>
                            Cancel
                        </Button>
                        <Button type="submit" disabled={isSubmitting || !password || !newEmail}>
                            {isSubmitting && <Loader2 className="mr-2 size-4 animate-spin" />}
                            Change email
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
};
