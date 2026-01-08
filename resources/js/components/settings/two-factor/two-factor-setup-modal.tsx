import { useState, useEffect } from 'react';
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
import { Copy, Check, Loader2 } from 'lucide-react';
import InputError from '@/components/input-error';

interface TwoFactorSetupModalProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    qrCodeSvg: string | null;
    secretKey: string | null;
    isLoading: boolean;
    errors: Record<string, string>;
    onConfirm: (code: string) => void;
    onClose: () => void;
}

export function TwoFactorSetupModal({
    open,
    onOpenChange,
    qrCodeSvg,
    secretKey,
    isLoading,
    errors,
    onConfirm,
    onClose,
}: TwoFactorSetupModalProps) {
    const [step, setStep] = useState<'scan' | 'verify'>('scan');
    const [code, setCode] = useState('');
    const [copied, setCopied] = useState(false);

    useEffect(() => {
        if (!open) {
            setStep('scan');
            setCode('');
            setCopied(false);
        }
    }, [open]);

    const handleCopySecretKey = async () => {
        if (secretKey) {
            await navigator.clipboard.writeText(secretKey);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        }
    };

    const handleContinue = () => {
        setStep('verify');
    };

    const handleBack = () => {
        setStep('scan');
        setCode('');
    };

    const handleConfirm = () => {
        if (code.length === 6) {
            onConfirm(code);
        }
    };

    const handleOpenChange = (open: boolean) => {
        if (!open) {
            onClose();
        }
        onOpenChange(open);
    };

    return (
        <Dialog open={open} onOpenChange={handleOpenChange}>
            <DialogContent className="sm:max-w-md">
                {step === 'scan' ? (
                    <>
                        <DialogHeader>
                            <DialogTitle>Enable Two-Factor Authentication</DialogTitle>
                            <DialogDescription>
                                Scan this QR code with your authenticator app (like Google Authenticator, Authy, or 1Password).
                            </DialogDescription>
                        </DialogHeader>

                        <div className="flex flex-col items-center gap-4 py-4">
                            {isLoading ? (
                                <div className="flex h-48 w-48 items-center justify-center">
                                    <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
                                </div>
                            ) : qrCodeSvg ? (
                                <div
                                    className="rounded-lg bg-white p-4"
                                    dangerouslySetInnerHTML={{ __html: qrCodeSvg }}
                                />
                            ) : null}

                            {secretKey && (
                                <div className="w-full space-y-2">
                                    <p className="text-center text-sm text-muted-foreground">
                                        Or enter this code manually:
                                    </p>
                                    <div className="flex items-center gap-2">
                                        <code className="flex-1 rounded-md bg-muted px-3 py-2 text-center font-mono text-sm">
                                            {secretKey}
                                        </code>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="icon"
                                            onClick={handleCopySecretKey}
                                        >
                                            {copied ? (
                                                <Check className="h-4 w-4" />
                                            ) : (
                                                <Copy className="h-4 w-4" />
                                            )}
                                        </Button>
                                    </div>
                                </div>
                            )}
                        </div>

                        <DialogFooter>
                            <Button
                                type="button"
                                onClick={handleContinue}
                                disabled={isLoading || !qrCodeSvg}
                            >
                                Continue
                            </Button>
                        </DialogFooter>
                    </>
                ) : (
                    <>
                        <DialogHeader>
                            <DialogTitle>Verify Authentication Code</DialogTitle>
                            <DialogDescription>
                                Enter the 6-digit code from your authenticator app to confirm setup.
                            </DialogDescription>
                        </DialogHeader>

                        <div className="py-4">
                            <Input
                                type="text"
                                inputMode="numeric"
                                pattern="[0-9]*"
                                maxLength={6}
                                value={code}
                                onChange={(e) => setCode(e.target.value.replace(/\D/g, '').slice(0, 6))}
                                placeholder="000000"
                                className="text-center text-2xl font-mono tracking-widest"
                                autoFocus
                                autoComplete="one-time-code"
                            />
                            {errors.code && (
                                <div className="mt-2">
                                    <InputError message={errors.code} />
                                </div>
                            )}
                        </div>

                        <DialogFooter className="gap-2 sm:gap-0">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={handleBack}
                            >
                                Back
                            </Button>
                            <Button
                                type="button"
                                onClick={handleConfirm}
                                disabled={code.length !== 6}
                            >
                                Confirm
                            </Button>
                        </DialogFooter>
                    </>
                )}
            </DialogContent>
        </Dialog>
    );
}
