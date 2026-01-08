import { useState } from 'react';
import { router } from '@inertiajs/react';
import { WorkspacePlan } from '@/types/workspace';

export interface Plan {
    plan: string;
    name: string;
    description: string;
    price: number;
    features: string[];
    recommended: boolean;
}

interface UseSelectPlanReturn {
    plans: Plan[];
    selectedPlan: WorkspacePlan | null;
    selectPlan: (plan: WorkspacePlan) => void;
    isLoading: boolean;
}

export const useSelectPlan = (
    plans: Plan[],
    workspaceName: string,
    preselectedPlan?: WorkspacePlan | null
): UseSelectPlanReturn => {
    const [selectedPlan, setSelectedPlan] = useState<WorkspacePlan | null>(preselectedPlan ?? null);
    const [isLoading, setIsLoading] = useState(false);

    const selectPlan = (plan: WorkspacePlan) => {
        setSelectedPlan(plan);
        setIsLoading(true);

        router.visit('/onboarding/invite-team', {
            data: {
                name: workspaceName,
                plan: plan,
            },
            onFinish: () => setIsLoading(false),
        });
    };

    return {
        plans,
        selectedPlan,
        selectPlan,
        isLoading,
    };
};
