import { Head, router, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useState } from 'react';
import { cn } from '@/lib/utils';
import { getPlans } from '@/config/plans';
import type { SharedData } from '@/types';
import { PlanCard } from '@/components/billing/plan-card';

interface ChangePlanPageProps extends SharedData {
    earlyBirdSlots: {
        starter: number;
        pro: number;
    };
}

export default function ChangePlan() {
    const { earlyBirdSlots, currentWorkspace } = usePage<ChangePlanPageProps>().props;
    const [isYearly, setIsYearly] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    const plans = getPlans(earlyBirdSlots);
    const currentPlan = currentWorkspace?.plan ?? 'free';

    const handleSelectPlan = (planId: string | null) => {
        setIsLoading(true);

        // Change plan (handles upgrades, downgrades, and cancellations)
        router.post('/billing/change-plan', {
            plan: planId ?? 'free',
            billing_period: isYearly ? 'yearly' : 'monthly',
        }, {
            onFinish: () => setIsLoading(false),
        });
    };

    return (
        <>
            <Head title="Change Plan - NotifyHub" />

            <div className="min-h-screen bg-background">
                <div className="container mx-auto px-4 py-12 max-w-7xl">
                    {/* Header */}
                    <div className="mb-12 text-center">
                        {!isLoading && (
                            <button
                                onClick={() => window.history.back()}
                                className="mb-6 inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
                            >
                                <ArrowLeft className="h-4 w-4" />
                                Back to Billing
                            </button>
                        )}
                        <h1 className="text-4xl font-bold text-foreground">
                            Select your plan
                        </h1>
                        <p className="mt-3 text-lg text-muted-foreground">
                            Upgrade or downgrade your workspace <strong>{currentWorkspace?.name}</strong>
                        </p>
                    </div>

                    {/* Billing Toggle */}
                    {!isLoading && (
                        <div className="mb-8 flex justify-center">
                            <div className="inline-flex items-center gap-3 rounded-lg border bg-muted p-1">
                                <button
                                    onClick={() => setIsYearly(false)}
                                    className={cn(
                                        'rounded-md px-4 py-2 text-sm font-medium transition-colors',
                                        !isYearly ? 'bg-background shadow-sm' : 'hover:text-foreground',
                                    )}
                                >
                                    Monthly
                                </button>
                                <button
                                    onClick={() => setIsYearly(true)}
                                    className={cn(
                                        'rounded-md px-4 py-2 text-sm font-medium transition-colors',
                                        isYearly ? 'bg-background shadow-sm' : 'hover:text-foreground',
                                    )}
                                >
                                    Yearly
                                    <span className="ml-1.5 text-emerald-600 dark:text-emerald-400">-20%</span>
                                </button>
                            </div>
                        </div>
                    )}

                    {/* Plan Cards - Horizontal Scrollable Layout like onboarding */}
                    <div className="overflow-x-auto py-4">
                        <div className="flex gap-6 min-w-min justify-center">
                            {plans.map((plan) => (
                                <div key={plan.id ?? 'free'} className="w-80 flex-shrink-0">
                                    <PlanCard
                                        plan={plan}
                                        isCurrent={(plan.id ?? 'free') === currentPlan}
                                        onSelect={() => handleSelectPlan(plan.id)}
                                        disabled={isLoading}
                                        billingPeriod={isYearly ? 'yearly' : 'monthly'}
                                    />
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Note */}
                    <div className="mt-8 max-w-3xl mx-auto rounded-lg border bg-muted/50 p-4 text-sm text-muted-foreground">
                        <p>
                            <strong>Note:</strong> When upgrading, you'll be charged the prorated difference immediately. When downgrading,
                            the change takes effect at the end of your current billing period.
                        </p>
                    </div>
                </div>
            </div>
        </>
    );
}
