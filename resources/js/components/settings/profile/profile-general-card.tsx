import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { UserAvatar } from '@/components/user-avatar';
import { Link } from '@inertiajs/react';
import { Loader2, Trash2, Upload } from 'lucide-react';
import { useRef } from 'react';

interface ProfileGeneralCardProps {
    name: string;
    onNameChange: (name: string) => void;
    onNameBlur: () => void;
    email: string;
    isSaving: boolean;
    mustVerifyEmail?: boolean;
    isEmailVerified: boolean;
    verificationStatus?: string;
    onChangeEmailClick: () => void;
    avatar: string | null;
    onAvatarUpload: (file: File) => void;
    onAvatarRemove: () => void;
    isUploadingAvatar?: boolean;
}

export const ProfileGeneralCard = ({
    name,
    onNameChange,
    onNameBlur,
    email,
    isSaving,
    mustVerifyEmail,
    isEmailVerified,
    verificationStatus,
    onChangeEmailClick,
    avatar,
    onAvatarUpload,
    onAvatarRemove,
    isUploadingAvatar,
}: ProfileGeneralCardProps) => {
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
            </CardHeader>
            <CardContent className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <Label>Profile picture</Label>
                        <p className="text-muted-foreground text-sm">Optional</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <UserAvatar name={name} avatar={avatar} size="lg" />
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
                    <Label htmlFor="name" className="min-w-32">
                        Full name
                    </Label>
                    <Input
                        id="name"
                        value={name}
                        onChange={(e) => onNameChange(e.target.value)}
                        onBlur={onNameBlur}
                        className="max-w-md"
                    />
                </div>

                <div className="flex items-center justify-between gap-4">
                    <Label htmlFor="email" className="min-w-32">
                        Email address
                    </Label>
                    <div className="flex max-w-md flex-1 items-center gap-2">
                        <Input
                            id="email"
                            type="email"
                            value={email}
                            className="flex-1"
                            disabled
                        />
                        <Button variant="outline" size="sm" onClick={onChangeEmailClick}>
                            Change
                        </Button>
                    </div>
                </div>

                {mustVerifyEmail && !isEmailVerified && (
                    <div className="rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/20">
                        <p className="text-sm">
                            Your email address is unverified.{' '}
                            <Link
                                href={route('verification.send')}
                                method="post"
                                as="button"
                                className="font-medium underline"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>
                        {verificationStatus === 'verification-link-sent' && (
                            <p className="mt-2 text-sm font-medium text-green-600">
                                A new verification link has been sent to your email address.
                            </p>
                        )}
                    </div>
                )}
            </CardContent>
        </Card>
    );
};
