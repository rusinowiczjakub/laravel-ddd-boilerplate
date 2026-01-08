import { type ReactNode, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Loader2 } from 'lucide-react';

interface LoadingButtonProps {
    children: ReactNode,
    loading?: boolean;
    loadingText?: string;
    onClick?: (e: React.MouseEvent<HTMLButtonElement>) => void | Promise<void>;
    disabled: boolean
}

export function LoadingButton({
    children,
    loading: externalLoading,
    loadingText,
    disabled,
    onClick,
    ...props
}: LoadingButtonProps) {
    const [internalLoading, setInternalLoading] = useState(false);

    const isLoading = externalLoading || internalLoading;

    const handleClick = async (e: React.MouseEvent<HTMLButtonElement>) => {
        if (!onClick || isLoading) return;

        const result = onClick(e);

        // Check if onClick returns a Promise
        if (result instanceof Promise) {
            setInternalLoading(true);
            try {
                await result;
            } finally {
                setInternalLoading(false);
            }
        }
    };

    return (
        <Button {...props} disabled={disabled || isLoading} onClick={handleClick}>
            {isLoading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
            {isLoading && loadingText ? loadingText : children}
        </Button>
    );
}
