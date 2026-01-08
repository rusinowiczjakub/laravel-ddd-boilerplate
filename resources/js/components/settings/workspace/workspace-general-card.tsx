import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { WorkspaceAvatar } from '@/components/workspace-avatar';
import { Loader2, Trash2, Upload } from 'lucide-react';
import { useRef } from 'react';

interface WorkspaceGeneralCardProps {
    workspaceName: string;
    onNameChange: (name: string) => void;
    onNameBlur: () => void;
    enforce2FA: boolean;
    onEnforce2FAChange: (enabled: boolean) => void;
    isSaving?: boolean;
    avatar: string | null;
    onAvatarUpload: (file: File) => void;
    onAvatarRemove: () => void;
    isUploadingAvatar?: boolean;
}

export const WorkspaceGeneralCard = ({
    workspaceName,
    onNameChange,
    onNameBlur,
    enforce2FA,
    onEnforce2FAChange,
    isSaving,
    avatar,
    onAvatarUpload,
    onAvatarRemove,
    isUploadingAvatar,
}: WorkspaceGeneralCardProps) => {
    const fileInputRef = useRef<HTMLInputElement>(null);

    const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            onAvatarUpload(file);
        }
    };

    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    General
                    {isSaving && <Loader2 className="size-4 animate-spin text-muted-foreground" />}
                </CardTitle>
                <p className="text-muted-foreground text-sm">General settings for your workspace</p>
            </CardHeader>
            <CardContent className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <Label>Icon</Label>
                        <p className="text-muted-foreground text-sm">Optional</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <WorkspaceAvatar name={workspaceName} avatar={avatar} size="lg" variant="default" />
                        <input
                            ref={fileInputRef}
                            type="file"
                            accept="image/*"
                            className="hidden"
                            onChange={handleFileSelect}
                        />
                        <Button
                            variant="outline"
                            onClick={() => fileInputRef.current?.click()}
                            disabled={isUploadingAvatar}
                        >
                            {isUploadingAvatar ? (
                                <Loader2 className="size-4 animate-spin" />
                            ) : (
                                <Upload className="size-4" />
                            )}
                        </Button>
                        {avatar && (
                            <Button
                                variant="outline"
                                onClick={onAvatarRemove}
                                disabled={isUploadingAvatar}
                            >
                                <Trash2 className="size-4" />
                            </Button>
                        )}
                    </div>
                </div>

                <div className="flex items-center justify-between gap-4">
                    <div className="flex-1">
                        <Label htmlFor="workspace-name">Workspace name</Label>
                        <p className="text-muted-foreground text-sm">Used for the NotifyHub app</p>
                    </div>
                    <Input
                        id="workspace-name"
                        value={workspaceName}
                        onChange={(e) => onNameChange(e.target.value)}
                        onBlur={onNameBlur}
                        className="max-w-md"
                    />
                </div>

                <div className="flex items-start justify-between gap-4">
                    <div className="flex-1">
                        <Label htmlFor="enforce-2fa">Enforce two-factor authentication</Label>
                        <p className="text-muted-foreground text-sm">
                            Require all members to use two-factor authentication to access this workspace
                        </p>
                    </div>
                    <Switch
                        id="enforce-2fa"
                        checked={enforce2FA}
                        onCheckedChange={onEnforce2FAChange}
                    />
                </div>
            </CardContent>
        </Card>
    );
};
