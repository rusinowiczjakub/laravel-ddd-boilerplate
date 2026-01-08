import { useCallback, useState } from 'react';
import { router } from '@inertiajs/react';

interface TwoFactorSetupData {
    qrCodeSvg: string | null;
    secretKey: string | null;
}

interface UseTwoFactorAuthReturn {
    setupData: TwoFactorSetupData;
    recoveryCodes: string[];
    isLoading: boolean;
    errors: Record<string, string>;
    hasSetupData: boolean;
    clearSetupData: () => void;
    clearErrors: () => void;
    fetchSetupData: () => Promise<void>;
    fetchRecoveryCodes: () => Promise<void>;
    enableTwoFactor: () => void;
    confirmTwoFactor: (code: string) => void;
    disableTwoFactor: () => void;
    regenerateRecoveryCodes: () => void;
}

export function useTwoFactorAuth(): UseTwoFactorAuthReturn {
    const [setupData, setSetupData] = useState<TwoFactorSetupData>({
        qrCodeSvg: null,
        secretKey: null,
    });
    const [recoveryCodes, setRecoveryCodes] = useState<string[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const clearSetupData = useCallback(() => {
        setSetupData({ qrCodeSvg: null, secretKey: null });
    }, []);

    const clearErrors = useCallback(() => {
        setErrors({});
    }, []);

    const fetchSetupData = useCallback(async () => {
        setIsLoading(true);
        try {
            const [qrCodeResponse, secretKeyResponse] = await Promise.all([
                fetch(route('user.two-factor.qr-code')),
                fetch(route('user.two-factor.secret-key')),
            ]);

            if (!qrCodeResponse.ok || !secretKeyResponse.ok) {
                throw new Error('Failed to fetch setup data');
            }

            const qrCodeData = await qrCodeResponse.json();
            const secretKeyData = await secretKeyResponse.json();

            setSetupData({
                qrCodeSvg: qrCodeData.svg,
                secretKey: secretKeyData.secretKey,
            });
        } catch (error) {
            setErrors({ fetch: 'Failed to load setup data' });
        } finally {
            setIsLoading(false);
        }
    }, []);

    const fetchRecoveryCodes = useCallback(async () => {
        setIsLoading(true);
        try {
            const response = await fetch(route('user.two-factor.recovery-codes'));

            if (!response.ok) {
                throw new Error('Failed to fetch recovery codes');
            }

            const data = await response.json();
            setRecoveryCodes(data.recoveryCodes);
        } catch (error) {
            setErrors({ fetch: 'Failed to load recovery codes' });
        } finally {
            setIsLoading(false);
        }
    }, []);

    const enableTwoFactor = useCallback(() => {
        router.post(route('user.two-factor.enable'), {}, {
            preserveScroll: true,
            onSuccess: () => {
                fetchSetupData();
            },
        });
    }, [fetchSetupData]);

    const confirmTwoFactor = useCallback((code: string) => {
        router.post(route('user.two-factor.confirm'), { code }, {
            preserveScroll: true,
            onError: (errors) => {
                setErrors(errors as Record<string, string>);
            },
        });
    }, []);

    const disableTwoFactor = useCallback(() => {
        router.delete(route('user.two-factor.disable'), {
            preserveScroll: true,
            onSuccess: () => {
                clearSetupData();
                setRecoveryCodes([]);
            },
        });
    }, [clearSetupData]);

    const regenerateRecoveryCodes = useCallback(() => {
        router.post(route('user.two-factor.regenerate-recovery-codes'), {}, {
            preserveScroll: true,
            onSuccess: () => {
                fetchRecoveryCodes();
            },
        });
    }, [fetchRecoveryCodes]);

    return {
        setupData,
        recoveryCodes,
        isLoading,
        errors,
        hasSetupData: !!setupData.qrCodeSvg && !!setupData.secretKey,
        clearSetupData,
        clearErrors,
        fetchSetupData,
        fetchRecoveryCodes,
        enableTwoFactor,
        confirmTwoFactor,
        disableTwoFactor,
        regenerateRecoveryCodes,
    };
}
