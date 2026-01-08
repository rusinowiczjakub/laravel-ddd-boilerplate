import { useCallback, useRef, useState } from 'react';
import { ConfirmationContext, type ConfirmationOptions } from '@/hooks/use-confirmation';
import { ConfirmationDialog } from '@/components/ui/confirmation-dialog';

interface ConfirmationProviderProps {
    children: React.ReactNode;
}

interface ConfirmationState extends ConfirmationOptions {
    isOpen: boolean;
}

const defaultState: ConfirmationState = {
    isOpen: false,
    title: '',
    description: undefined,
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    variant: 'default',
    requireTypedConfirmation: undefined,
};

export function ConfirmationProvider({ children }: ConfirmationProviderProps) {
    const [state, setState] = useState<ConfirmationState>(defaultState);
    const [typedValue, setTypedValue] = useState('');
    const resolveRef = useRef<((value: boolean) => void) | null>(null);

    const confirm = useCallback((options: ConfirmationOptions): Promise<boolean> => {
        return new Promise((resolve) => {
            resolveRef.current = resolve;
            setTypedValue('');
            setState({
                isOpen: true,
                title: options.title,
                description: options.description,
                confirmText: options.confirmText ?? 'Confirm',
                cancelText: options.cancelText ?? 'Cancel',
                variant: options.variant ?? 'destructive',
                requireTypedConfirmation: options.requireTypedConfirmation,
            });
        });
    }, []);

    const handleConfirm = useCallback(() => {
        setState(defaultState);
        resolveRef.current?.(true);
        resolveRef.current = null;
    }, []);

    const handleCancel = useCallback(() => {
        setState(defaultState);
        resolveRef.current?.(false);
        resolveRef.current = null;
    }, []);

    return (
        <ConfirmationContext.Provider value={{ confirm }}>
            {children}
            <ConfirmationDialog
                open={state.isOpen}
                title={state.title}
                description={state.description}
                confirmText={state.confirmText ?? 'Confirm'}
                cancelText={state.cancelText ?? 'Cancel'}
                variant={state.variant ?? 'destructive'}
                requireTypedConfirmation={state.requireTypedConfirmation}
                typedValue={typedValue}
                onTypedValueChange={setTypedValue}
                onConfirm={handleConfirm}
                onCancel={handleCancel}
            />
        </ConfirmationContext.Provider>
    );
}
