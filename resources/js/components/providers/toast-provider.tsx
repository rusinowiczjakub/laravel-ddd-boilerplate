import { useFlashToast } from '@/hooks/use-flash-toast';
import { Toaster } from '@/components/ui/sonner';

interface ToastProviderProps {
    children: React.ReactNode;
}

function FlashToastHandler() {
    useFlashToast();
    return null;
}

export function ToastProvider({ children }: ToastProviderProps) {
    return (
        <>
            {children}
            <FlashToastHandler />
            <Toaster position="bottom-center" />
        </>
    );
}
