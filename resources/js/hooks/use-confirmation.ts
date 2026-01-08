import { createContext, useContext } from 'react';

export interface ConfirmationOptions {
    title: string;
    description?: string;
    confirmText?: string;
    cancelText?: string;
    variant?: 'destructive' | 'default';
    /**
     * If set, user must type this exact string to confirm.
     * Useful for dangerous operations like deletion.
     */
    requireTypedConfirmation?: string;
}

export interface ConfirmationContextValue {
    confirm: (options: ConfirmationOptions) => Promise<boolean>;
}

export const ConfirmationContext = createContext<ConfirmationContextValue | null>(null);

export function useConfirmation(): ConfirmationContextValue {
    const context = useContext(ConfirmationContext);

    if (!context) {
        throw new Error('useConfirmation must be used within a ConfirmationProvider');
    }

    return context;
}
