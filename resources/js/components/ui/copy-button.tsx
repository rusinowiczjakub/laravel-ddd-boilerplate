import { Button, buttonVariants } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { type VariantProps } from 'class-variance-authority';
import { Check, Copy } from 'lucide-react';
import { useState } from 'react';

interface CopyButtonProps extends VariantProps<typeof buttonVariants> {
    value: string;
    className?: string;
}

export function CopyButton({ value, variant = 'outline', size = 'icon', className }: CopyButtonProps) {
    const [copied, setCopied] = useState(false);

    const handleCopy = async () => {
        try {
            await navigator.clipboard.writeText(value);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    };

    return (
        <Button
            type="button"
            variant={variant}
            size={size}
            className={cn(className)}
            onClick={handleCopy}
        >
            {copied ? <Check className="h-4 w-4 text-green-500" /> : <Copy className="h-4 w-4" />}
        </Button>
    );
}
