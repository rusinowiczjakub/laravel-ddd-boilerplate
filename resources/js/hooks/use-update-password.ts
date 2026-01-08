import { useForm } from '@inertiajs/react';
import { useCallback, useState } from 'react';

interface UpdatePasswordData {
    current_password: string;
    password: string;
    password_confirmation: string;
}

interface UpdatePasswordErrors {
    current_password?: string;
    password?: string;
    password_confirmation?: string;
}

interface UseUpdatePasswordReturn {
    isModalOpen: boolean;
    openModal: () => void;
    closeModal: () => void;
    currentPassword: string;
    setCurrentPassword: (value: string) => void;
    newPassword: string;
    setNewPassword: (value: string) => void;
    confirmPassword: string;
    setConfirmPassword: (value: string) => void;
    submit: () => void;
    isSubmitting: boolean;
    errors: UpdatePasswordErrors;
    recentlySuccessful: boolean;
}

export function useUpdatePassword(): UseUpdatePasswordReturn {
    const [isModalOpen, setIsModalOpen] = useState(false);

    const { data, setData, put, processing, errors, reset, recentlySuccessful } =
        useForm<UpdatePasswordData>({
            current_password: '',
            password: '',
            password_confirmation: '',
        });

    const openModal = useCallback(() => {
        setIsModalOpen(true);
    }, []);

    const closeModal = useCallback(() => {
        setIsModalOpen(false);
        reset();
    }, [reset]);

    const submit = useCallback(() => {
        put(route('settings.password.update'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    }, [put, closeModal]);

    return {
        isModalOpen,
        openModal,
        closeModal,
        currentPassword: data.current_password,
        setCurrentPassword: (value: string) => setData('current_password', value),
        newPassword: data.password,
        setNewPassword: (value: string) => setData('password', value),
        confirmPassword: data.password_confirmation,
        setConfirmPassword: (value: string) => setData('password_confirmation', value),
        submit,
        isSubmitting: processing,
        errors: {
            current_password: errors.current_password,
            password: errors.password,
            password_confirmation: errors.password_confirmation,
        },
        recentlySuccessful,
    };
}
