import { router, usePage } from '@inertiajs/react';
import { useCallback, useRef, useState } from 'react';
import { type SharedData } from '@/types';

interface UseWorkspaceSettingsReturn {
    workspaceName: string;
    setWorkspaceName: (name: string) => void;
    handleNameBlur: () => void;
    enforce2FA: boolean;
    setEnforce2FA: (enabled: boolean) => void;
    isSaving: boolean;
    uploadAvatar: (file: File) => void;
    removeAvatar: () => void;
    isUploadingAvatar: boolean;
    avatar: string | null;
}

export const useWorkspaceSettings = (): UseWorkspaceSettingsReturn => {
    const { currentWorkspace } = usePage<SharedData>().props;

    const [workspaceName, setWorkspaceName] = useState(currentWorkspace?.name ?? '');
    const [enforce2FA, setEnforce2FA] = useState(false);
    const [isSaving, setIsSaving] = useState(false);
    const [isUploadingAvatar, setIsUploadingAvatar] = useState(false);

    const originalName = useRef(currentWorkspace?.name ?? '');

    const handleNameBlur = useCallback(() => {
        const trimmedName = workspaceName.trim();
        if (trimmedName === originalName.current || !trimmedName || !currentWorkspace) {
            return;
        }

        setIsSaving(true);
        router.patch(
            `/workspaces/${currentWorkspace.id}`,
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
    }, [workspaceName, currentWorkspace]);

    const handleEnforce2FAChange = useCallback(
        (enabled: boolean) => {
            if (!currentWorkspace) return;

            setEnforce2FA(enabled);
            router.patch(
                `/workspaces/${currentWorkspace.id}`,
                { enforce_2fa: enabled },
                { preserveScroll: true }
            );
        },
        [currentWorkspace]
    );

    const uploadAvatar = useCallback(
        (file: File) => {
            if (!currentWorkspace) return;

            setIsUploadingAvatar(true);
            router.post(
                `/workspaces/${currentWorkspace.id}`,
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
        },
        [currentWorkspace]
    );

    const removeAvatar = useCallback(() => {
        if (!currentWorkspace) return;

        setIsUploadingAvatar(true);
        router.patch(
            `/workspaces/${currentWorkspace.id}`,
            { remove_avatar: true },
            {
                preserveScroll: true,
                onFinish: () => {
                    setIsUploadingAvatar(false);
                },
            }
        );
    }, [currentWorkspace]);

    return {
        workspaceName,
        setWorkspaceName,
        handleNameBlur,
        enforce2FA,
        setEnforce2FA: handleEnforce2FAChange,
        isSaving,
        uploadAvatar,
        removeAvatar,
        isUploadingAvatar,
        avatar: currentWorkspace?.avatar ?? null,
    };
};
