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

interface UpdatePasswordModalProps {
    isOpen: boolean;
    onClose: () => void;
    currentPassword: string;
    onCurrentPasswordChange: (value: string) => void;
    newPassword: string;
    onNewPasswordChange: (value: string) => void;
    confirmPassword: string;
    onConfirmPasswordChange: (value: string) => void;
    onSubmit: () => void;
    isSubmitting: boolean;
    errors: {
        current_password?: string;
        password?: string;
        password_confirmation?: string;
    };
}

export const UpdatePasswordModal = ({
    isOpen,
    onClose,
    currentPassword,
    onCurrentPasswordChange,
    newPassword,
    onNewPasswordChange,
    confirmPassword,
    onConfirmPasswordChange,
    onSubmit,
    isSubmitting,
    errors,
}: UpdatePasswordModalProps) => {
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        onSubmit();
    };

    const isValid = currentPassword && newPassword && confirmPassword;

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Update password</DialogTitle>
                    <DialogDescription>
                        Ensure your account is using a long, random password to stay secure.
                    </DialogDescription>
                </DialogHeader>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="space-y-2">
                        <Label htmlFor="current-password">Current password</Label>
                        <Input
                            id="current-password"
                            type="password"
                            value={currentPassword}
                            onChange={(e) => onCurrentPasswordChange(e.target.value)}
                            placeholder="Enter your current password"
                            autoComplete="current-password"
                        />
                        {errors.current_password && (
                            <p className="text-sm text-red-500">{errors.current_password}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="new-password">New password</Label>
                        <Input
                            id="new-password"
                            type="password"
                            value={newPassword}
                            onChange={(e) => onNewPasswordChange(e.target.value)}
                            placeholder="Enter your new password"
                            autoComplete="new-password"
                        />
                        {errors.password && (
                            <p className="text-sm text-red-500">{errors.password}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="confirm-password">Confirm password</Label>
                        <Input
                            id="confirm-password"
                            type="password"
                            value={confirmPassword}
                            onChange={(e) => onConfirmPasswordChange(e.target.value)}
                            placeholder="Confirm your new password"
                            autoComplete="new-password"
                        />
                        {errors.password_confirmation && (
                            <p className="text-sm text-red-500">{errors.password_confirmation}</p>
                        )}
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={onClose}>
                            Cancel
                        </Button>
                        <Button type="submit" disabled={isSubmitting || !isValid}>
                            {isSubmitting && <Loader2 className="mr-2 size-4 animate-spin" />}
                            Update password
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
};
