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

interface ConfirmationDialogProps {
    open: boolean;
    title: string;
    description?: string;
    confirmText: string;
    cancelText: string;
    variant: 'destructive' | 'default';
    requireTypedConfirmation?: string;
    typedValue: string;
    onTypedValueChange: (value: string) => void;
    onConfirm: () => void;
    onCancel: () => void;
}

export function ConfirmationDialog({
    open,
    title,
    description,
    confirmText,
    cancelText,
    variant,
    requireTypedConfirmation,
    typedValue,
    onTypedValueChange,
    onConfirm,
    onCancel,
}: ConfirmationDialogProps) {
    const isConfirmDisabled = requireTypedConfirmation
        ? typedValue !== requireTypedConfirmation
        : false;

    const handleKeyDown = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter' && !isConfirmDisabled) {
            onConfirm();
        }
    };

    return (
        <Dialog open={open} onOpenChange={(isOpen) => !isOpen && onCancel()}>
            <DialogContent onKeyDown={handleKeyDown}>
                <DialogHeader>
                    <DialogTitle>{title}</DialogTitle>
                    {description && <DialogDescription>{description}</DialogDescription>}
                </DialogHeader>

                {requireTypedConfirmation && (
                    <div className="space-y-2 py-2">
                        <Label htmlFor="confirmation-input">
                            Type <span className="font-mono font-bold">{requireTypedConfirmation}</span> to confirm
                        </Label>
                        <Input
                            id="confirmation-input"
                            value={typedValue}
                            onChange={(e) => onTypedValueChange(e.target.value)}
                            placeholder={requireTypedConfirmation}
                            autoFocus
                            autoComplete="off"
                        />
                    </div>
                )}

                <DialogFooter>
                    <Button variant="outline" onClick={onCancel}>
                        {cancelText}
                    </Button>
                    <Button
                        variant={variant === 'destructive' ? 'destructive' : 'default'}
                        onClick={onConfirm}
                        disabled={isConfirmDisabled}
                    >
                        {confirmText}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
