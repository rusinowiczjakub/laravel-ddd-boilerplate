import { useState, useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Eye, EyeOff, RefreshCw, Loader2 } from 'lucide-react';
import { useConfirmation } from '@/hooks/use-confirmation';

interface TwoFactorRecoveryCodesProps {
    recoveryCodes: string[];
    isLoading: boolean;
    onFetch: () => void;
    onRegenerate: () => void;
}

export function TwoFactorRecoveryCodes({
    recoveryCodes,
    isLoading,
    onFetch,
    onRegenerate,
}: TwoFactorRecoveryCodesProps) {
    const [visible, setVisible] = useState(false);
    const { confirm } = useConfirmation();

    useEffect(() => {
        if (visible && recoveryCodes.length === 0) {
            onFetch();
        }
    }, [visible, recoveryCodes.length, onFetch]);

    const handleToggleVisibility = () => {
        setVisible(!visible);
    };

    const handleRegenerate = async () => {
        const confirmed = await confirm({
            title: 'Regenerate Recovery Codes',
            description: 'Are you sure you want to regenerate your recovery codes? Your old codes will no longer work.',
            confirmText: 'Regenerate',
            variant: 'destructive',
        });
        if (confirmed) {
            onRegenerate();
        }
    };

    return (
        <div className="space-y-4">
            <div className="flex items-center justify-between">
                <div>
                    <h4 className="text-sm font-medium">Recovery Codes</h4>
                    <p className="text-sm text-muted-foreground">
                        Store these codes in a safe place. Each code can only be used once.
                    </p>
                </div>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    onClick={handleToggleVisibility}
                >
                    {visible ? (
                        <>
                            <EyeOff className="mr-2 h-4 w-4" />
                            Hide
                        </>
                    ) : (
                        <>
                            <Eye className="mr-2 h-4 w-4" />
                            View
                        </>
                    )}
                </Button>
            </div>

            {visible && (
                <div className="space-y-4">
                    {isLoading ? (
                        <div className="flex items-center justify-center py-8">
                            <Loader2 className="h-6 w-6 animate-spin text-muted-foreground" />
                        </div>
                    ) : (
                        <>
                            <div className="grid grid-cols-2 gap-2 rounded-lg bg-muted p-4">
                                {recoveryCodes.map((code, index) => (
                                    <code key={index} className="font-mono text-sm">
                                        {code}
                                    </code>
                                ))}
                            </div>

                            <div className="flex justify-end">
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    onClick={handleRegenerate}
                                >
                                    <RefreshCw className="mr-2 h-4 w-4" />
                                    Regenerate Codes
                                </Button>
                            </div>

                            <p className="text-xs text-muted-foreground">
                                Save these codes in a secure location like a password manager.
                                If you lose access to your authenticator app, you can use these codes to regain access to your account.
                            </p>
                        </>
                    )}
                </div>
            )}
        </div>
    );
}
