import { router } from '@inertiajs/react';
import { useCallback, useState } from 'react';

interface UseChangeEmailReturn {
    isModalOpen: boolean;
    openModal: () => void;
    closeModal: () => void;
    password: string;
    setPassword: (password: string) => void;
    newEmail: string;
    setNewEmail: (email: string) => void;
    submit: () => void;
    isSubmitting: boolean;
    errors: {
        password?: string;
        email?: string;
    };
}

export const useChangeEmail = (): UseChangeEmailReturn => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [password, setPassword] = useState('');
    const [newEmail, setNewEmail] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState<{ password?: string; email?: string }>({});

    const openModal = useCallback(() => {
        setIsModalOpen(true);
        setPassword('');
        setNewEmail('');
        setErrors({});
    }, []);

    const closeModal = useCallback(() => {
        setIsModalOpen(false);
        setPassword('');
        setNewEmail('');
        setErrors({});
    }, []);

    const submit = useCallback(() => {
        setIsSubmitting(true);
        setErrors({});

        router.post(
            '/settings/profile/email',
            {
                password,
                email: newEmail,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal();
                    // User will be redirected to verification page automatically
                    // since email_verified_at is now null
                },
                onError: (responseErrors) => {
                    setErrors({
                        password: responseErrors.password,
                        email: responseErrors.email,
                    });
                },
                onFinish: () => {
                    setIsSubmitting(false);
                },
            }
        );
    }, [password, newEmail, closeModal]);

    return {
        isModalOpen,
        openModal,
        closeModal,
        password,
        setPassword,
        newEmail,
        setNewEmail,
        submit,
        isSubmitting,
        errors,
    };
};
