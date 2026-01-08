import { Button } from '@/components/ui/button';
import { CheckIcon } from 'lucide-react';
import { cn } from '@/lib/utils';

interface PlanCardProps {
    name: string;
    description: string;
    price: number;
    features: string[];
    recommended?: boolean;
    onSelect: () => void;
    isSelected?: boolean;
}

export const PlanCard = ({
    name,
    description,
    price,
    features,
    recommended = false,
    onSelect,
    isSelected = false,
}: PlanCardProps) => {
    return (
        <div
            className={cn(
                'relative flex h-full flex-col rounded-xl border bg-card p-8 transition-all overflow-hidden',
                recommended ? 'border-success' : 'border-border',
                isSelected && !recommended && 'border-primary'
            )}
        >
            {/* Subtle green gradient overlay for recommended plan */}
            {recommended && (
                <div className="pointer-events-none absolute inset-0 bg-gradient-to-br from-success/5 via-transparent to-success/10" />
            )}

            {recommended && (
                <div className="absolute -top-3 left-1/2 z-10 -translate-x-1/2">
                    <span className="rounded-full bg-success px-3 py-1 text-xs font-medium text-success-foreground">
                        Recommended
                    </span>
                </div>
            )}

            {/* Header */}
            <div className="mb-6">
                <h3 className="text-2xl font-semibold text-foreground">{name}</h3>
                <p className="mt-1 text-sm text-muted-foreground">{description}</p>
            </div>

            {/* Price */}
            <div className="mb-6">
                <div className="flex items-baseline gap-1">
                    <span className="text-5xl font-bold text-foreground">${price}</span>
                    <span className="text-muted-foreground">/mo</span>
                </div>
            </div>

            {/* CTA Button */}
            <Button
                onClick={onSelect}
                className="mb-8 w-full"
                variant={recommended ? 'default' : 'secondary'}
                size="lg"
            >
                Start with {name}
            </Button>

            {/* Features */}
            <div className="flex-1 space-y-3">
                {features.map((feature, index) => (
                    <div key={index} className="flex items-start gap-3">
                        <CheckIcon className="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                        <span className="text-sm text-muted-foreground">{feature}</span>
                    </div>
                ))}
            </div>
        </div>
    );
};
