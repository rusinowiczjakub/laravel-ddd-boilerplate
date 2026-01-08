import { useAppearance, type Appearance } from '@/hooks/use-appearance';
import { type SharedData } from '@/types';
import { router, usePage } from '@inertiajs/react';
import { useCallback, useRef, useState } from 'react';

interface UseProfileSettingsReturn {
    name: string;
    setName: (name: string) => void;
    email: string;
    avatar: string | null;
    twoFactorEnabled: boolean;
    theme: Appearance;
    setTheme: (theme: Appearance) => void;
    isSaving: boolean;
    handleNameBlur: () => void;
    uploadAvatar: (file: File) => void;
    removeAvatar: () => void;
    isUploadingAvatar: boolean;
}

export const useProfileSettings = (): UseProfileSettingsReturn => {
    const { auth } = usePage<SharedData>().props;
    const { appearance, updateAppearance } = useAppearance();

    // Build full name from first_name + last_name if available, otherwise use name
    const getFullName = () => {
        const user = auth.user;
        if (user?.first_name && user?.last_name) {
            return `${user.first_name} ${user.last_name}`;
        }
        return user?.name ?? '';
    };

    const [name, setName] = useState(getFullName());
    const twoFactorEnabled = auth.user?.two_factor_confirmed_at !== null;
    const [isSaving, setIsSaving] = useState(false);
    const [isUploadingAvatar, setIsUploadingAvatar] = useState(false);

    // Track original values for dirty checking
    const originalName = useRef(getFullName());

    const handleNameBlur = useCallback(() => {
        const trimmedName = name.trim();
        if (trimmedName === originalName.current || !trimmedName) {
            return;
        }

        setIsSaving(true);
        router.patch(
            '/settings/profile',
            { name: trimmedName },
            {
                preserveScroll: true,
                onSuccess: () => {
                    originalName.current = trimmedName;
                },
                onFinish: () => {
                    setIsSaving(false);
                },
            }
        );
    }, [name]);

    const uploadAvatar = useCallback((file: File) => {
        setIsUploadingAvatar(true);
        router.post(
            '/settings/profile',
            {
                avatar: file,
                _method: 'PATCH',
            },
            {
                preserveScroll: true,
                forceFormData: true,
                onFinish: () => {
                    setIsUploadingAvatar(false);
                },
            }
        );
    }, []);

    const removeAvatar = useCallback(() => {
        setIsUploadingAvatar(true);
        router.patch(
            '/settings/profile',
            { remove_avatar: true },
            {
                preserveScroll: true,
                onFinish: () => {
                    setIsUploadingAvatar(false);
                },
            }
        );
    }, []);

    return {
        name,
        setName,
        email: auth.user?.email ?? '',
        avatar: (auth.user?.avatar as string) ?? null,
        twoFactorEnabled,
        theme: appearance,
        setTheme: updateAppearance,
        isSaving,
        handleNameBlur,
        uploadAvatar,
        removeAvatar,
        isUploadingAvatar,
    };
};
