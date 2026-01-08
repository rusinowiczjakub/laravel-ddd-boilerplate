import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';

interface FlashMessages {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

export function useFlashToast() {
    const { flash } = usePage().props as { flash: FlashMessages };

    useEffect(() => {
        if (!flash) return;

        if (flash.success) {
            toast.success(flash.success, {
                id: `success-${Date.now()}-${Math.random()}`,
            });
        }

        if (flash.error) {
            toast.error(flash.error, {
                id: `error-${Date.now()}-${Math.random()}`,
            });
        }

        if (flash.warning) {
            toast.warning(flash.warning, {
                id: `warning-${Date.now()}-${Math.random()}`,
            });
        }

        if (flash.info) {
            toast.info(flash.info, {
                id: `info-${Date.now()}-${Math.random()}`,
            });
        }
    }, [flash]);
}
