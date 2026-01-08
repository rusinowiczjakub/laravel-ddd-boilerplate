import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { TwoFactorRecoveryCodes } from '@/components/settings/two-factor';

interface ProfileSecurityCardProps {
    twoFactorEnabled: boolean;
    onTwoFactorToggle: (enabled: boolean) => void;
    onUpdatePassword: () => void;
    recoveryCodes: string[];
    isLoadingRecoveryCodes: boolean;
    onFetchRecoveryCodes: () => void;
    onRegenerateRecoveryCodes: () => void;
}

export const ProfileSecurityCard = ({
    twoFactorEnabled,
    onTwoFactorToggle,
    onUpdatePassword,
    recoveryCodes,
    isLoadingRecoveryCodes,
    onFetchRecoveryCodes,
    onRegenerateRecoveryCodes,
}: ProfileSecurityCardProps) => {
    return (
        <Card>
            <CardHeader>
                <CardTitle>Security</CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
                <div className="flex items-center justify-between">
                    <Label>Password</Label>
                    <Button variant="outline" onClick={onUpdatePassword}>
                        Update password
                    </Button>
                </div>

                <div className="flex items-start justify-between gap-4">
                    <div className="flex-1">
                        <Label htmlFor="two-factor">Two-factor authentication</Label>
                        <p className="text-muted-foreground text-sm">
                            Protect your account with additional verification code on login
                        </p>
                    </div>
                    <Switch
                        id="two-factor"
                        checked={twoFactorEnabled}
                        onCheckedChange={onTwoFactorToggle}
                    />
                </div>

                {twoFactorEnabled && (
                    <TwoFactorRecoveryCodes
                        recoveryCodes={recoveryCodes}
                        isLoading={isLoadingRecoveryCodes}
                        onFetch={onFetchRecoveryCodes}
                        onRegenerate={onRegenerateRecoveryCodes}
                    />
                )}
            </CardContent>
        </Card>
    );
};
