import { cn } from '@/lib/utils';
import { Check } from 'lucide-react';
import type { Plan } from '@/config/plans';
import { Button } from '@/components/ui/button';

interface PlanCardProps {
    plan: Plan;
    isSelected?: boolean;
    isCurrent?: boolean;
    onSelect: () => void;
    disabled?: boolean;
    ctaText?: string;
    billingPeriod?: 'monthly' | 'yearly';
}

export function PlanCard({
    plan,
    isSelected = false,
    isCurrent = false,
    onSelect,
    disabled = false,
    ctaText,
    billingPeriod = 'monthly'
}: PlanCardProps) {
    const regularPrice = billingPeriod === 'yearly' ? plan.price.yearly : plan.price.monthly;
    const earlyBirdPrice = billingPeriod === 'yearly'
        ? (plan.earlyBirdPrice?.yearly ?? null)
        : (plan.earlyBirdPrice?.monthly ?? null);
    const displayPrice = earlyBirdPrice ?? regularPrice;

    const defaultCtaText = isCurrent
        ? 'Current Plan'
        : `Switch to ${plan.name}`;

    return (
        <div
            className={cn(
                'relative flex h-full flex-col rounded-xl border bg-card p-8 transition-all',
                plan.earlyBirdPrice
                    ? 'border-emerald-500/50 bg-emerald-500/5'
                    : isCurrent
                        ? 'border-primary'
                        : plan.popular
                            ? 'border-success'
                            : 'border-border',
                isSelected && 'ring-2 ring-primary',
            )}
        >
            {/* Subtle gradient overlay */}
            {(plan.earlyBirdPrice || plan.popular || isCurrent) && (
                <div className="pointer-events-none absolute inset-0 bg-gradient-to-br from-success/5 via-transparent to-success/10" />
            )}

            {/* Current Plan Badge */}
            {isCurrent && (
                <div className="absolute -top-3 left-1/2 z-10 -translate-x-1/2">
                    <span className="rounded-full bg-primary px-3 py-1 text-xs font-medium text-primary-foreground">
                        Current Plan
                    </span>
                </div>
            )}

            {/* Popular Badge */}
            {plan.popular && !isCurrent && (
                <div className="absolute -top-3 left-1/2 z-10 -translate-x-1/2">
                    <span className="rounded-full bg-emerald-500 px-3 py-1 text-xs font-medium text-white">
                        Most Popular
                    </span>
                </div>
            )}


            {/* Header */}
            <div className="mb-6">
                <h3 className="text-2xl font-semibold text-foreground">{plan.name}</h3>
                <p className="mt-1 text-sm text-muted-foreground">{plan.description}</p>
            </div>

            {/* Price */}
            <div className="mb-6">
                {displayPrice > 0 ? (
                    <div>
                        <div className="flex items-baseline gap-2">
                            {earlyBirdPrice && (
                                <span className="text-2xl text-muted-foreground line-through">${regularPrice}</span>
                            )}
                            <span className="text-5xl font-bold text-foreground">${displayPrice}</span>
                            <span className="text-muted-foreground">/mo</span>
                        </div>
                        {earlyBirdPrice && (
                            <p className="mt-2 text-sm text-emerald-600 dark:text-emerald-400">
                                🎉 Lifetime price - lock in early-bird rate forever!
                            </p>
                        )}
                    </div>
                ) : (
                    <div className="text-5xl font-bold text-foreground">Free</div>
                )}
            </div>

            {/* CTA Button */}
            <Button
                onClick={onSelect}
                disabled={disabled || isCurrent}
                className="mb-8 w-full"
                variant={isCurrent ? 'secondary' : plan.popular ? 'default' : 'secondary'}
                size="lg"
            >
                {ctaText ?? defaultCtaText}
            </Button>

            {/* Features */}
            <div className="flex-1 space-y-3">
                {plan.features.map((feature) => (
                    <div key={feature} className="flex items-start gap-3">
                        <Check className="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                        <span className="text-sm text-muted-foreground">{feature}</span>
                    </div>
                ))}
            </div>
        </div>
    );
}
