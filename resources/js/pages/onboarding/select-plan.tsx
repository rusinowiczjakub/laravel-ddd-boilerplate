import OnboardingLayout from '@/layouts/onboarding-layout';
import { usePage, router } from '@inertiajs/react';
import { getPlans } from '@/config/plans';
import { useState } from 'react';
import { PlanCard } from '@/components/billing/plan-card';

interface SelectPlanProps {
    earlyBirdSlots: {
        starter: number;
        pro: number;
    };
    workspaceName: string;
    preselectedPlan?: string | null;
}

export default function SelectPlan() {
    const { earlyBirdSlots, workspaceName, preselectedPlan } = usePage<SelectPlanProps>().props;
    const plans = getPlans(earlyBirdSlots);
    const [selectedPlan, setSelectedPlan] = useState<string | null>(preselectedPlan ?? null);
    const [isLoading, setIsLoading] = useState(false);

    const goBack = () => {
        router.visit('/onboarding/create-workspace');
    };

    const selectPlan = (planId: string | null) => {
        setSelectedPlan(planId);
        setIsLoading(true);

        router.visit('/onboarding/invite-team', {
            data: {
                name: workspaceName,
                plan: planId ?? 'free',
            },
            onFinish: () => setIsLoading(false),
        });
    };

    return (
        <OnboardingLayout
            title="Select Plan"
            showBack={!isLoading}
            onBack={goBack}
        >
            <div className="w-full max-w-7xl mx-auto overflow-visible">
                {/* Header */}
                <div className="mb-12 text-center">
                    <h1 className="text-4xl font-bold text-foreground">
                        Select your plan
                    </h1>
                    <p className="mt-3 text-lg text-muted-foreground">
                        Pick one of the following NotifyHub plans for <strong>{workspaceName}</strong>.
                    </p>
                </div>

                {/* Plan Cards - Horizontal Scrollable Layout */}
                <div className="overflow-x-auto py-4">
                    <div className="flex gap-6 min-w-min justify-center">
                        {plans.map((plan) => (
                            <div key={plan.id ?? 'free'} className="w-80 flex-shrink-0">
                                <PlanCard
                                    plan={plan}
                                    isSelected={selectedPlan === (plan.id ?? 'free')}
                                    onSelect={() => selectPlan(plan.id)}
                                    disabled={isLoading}
                                    ctaText={`Start with ${plan.name}`}
                                />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </OnboardingLayout>
    );
}
