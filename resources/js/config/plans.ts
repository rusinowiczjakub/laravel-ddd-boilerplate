/**
 * Centralized pricing configuration for NotifyHub.
 *
 * This config is used across:
 * - /pricing page
 * - /onboarding/select-plan
 * - /settings/billing
 */

export interface PlanFeature {
    text: string;
    included: boolean;
}

export interface PlanPrice {
    monthly: number;
    yearly: number;
}

export interface Plan {
    id: string | null; // null for free plan
    name: string;
    description: string;
    price: PlanPrice;
    earlyBirdPrice?: PlanPrice;
    features: string[];
    eventLimit: number;
    workflowLimit: number;
    teamMemberLimit: number;
    logRetentionDays: number;
    support: string;
    popular?: boolean;
    recommended?: boolean;
}

export const PLAN_CONFIGS: Record<string, Omit<Plan, 'id'>> = {
    free: {
        name: 'Free',
        description: 'Perfect for testing',
        price: { monthly: 0, yearly: 0 },
        features: [
            '1,000 events per month',
            '5 workflows',
            '1 team member',
            '7 days log retention',
            'Community support',
        ],
        eventLimit: 1000,
        workflowLimit: 5,
        teamMemberLimit: 1,
        logRetentionDays: 7,
        support: 'Community',
    },
    starter: {
        name: 'Starter',
        description: 'For small teams',
        price: { monthly: 19, yearly: 15 },
        earlyBirdPrice: { monthly: 9, yearly: 7 },
        features: [
            '10,000 events per month',
            '20 workflows',
            '5 team members',
            '30 days log retention',
            'Email support',
        ],
        eventLimit: 10000,
        workflowLimit: 20,
        teamMemberLimit: 5,
        logRetentionDays: 30,
        support: 'Email',
    },
    pro: {
        name: 'Pro',
        description: 'For growing businesses',
        price: { monthly: 49, yearly: 39 },
        earlyBirdPrice: { monthly: 29, yearly: 23 },
        features: [
            '100,000 events per month',
            'Unlimited workflows',
            '15 team members',
            '90 days log retention',
            'Priority support',
        ],
        eventLimit: 100000,
        workflowLimit: -1, // unlimited
        teamMemberLimit: 15,
        logRetentionDays: 90,
        support: 'Priority',
        popular: false,
        recommended: false,
    },
};

/**
 * Get all plans with dynamic early-bird slots.
 */
export function getPlans(earlyBirdSlots?: { starter: number; pro: number }): Plan[] {
    return [
        {
            id: null,
            ...PLAN_CONFIGS.free,
        },
        {
            id: 'starter',
            ...PLAN_CONFIGS.starter,
            earlyBirdSlotsLeft: earlyBirdSlots?.starter,
        },
        {
            id: 'pro',
            ...PLAN_CONFIGS.pro,
            earlyBirdSlotsLeft: earlyBirdSlots?.pro,
        },
    ] as Plan[];
}

/**
 * Get plan config by ID.
 */
export function getPlanById(planId: string | null): Plan | undefined {
    if (!planId) {
        return { id: null, ...PLAN_CONFIGS.free };
    }

    const config = PLAN_CONFIGS[planId];
    if (!config) return undefined;

    return {
        id: planId,
        ...config,
    };
}

/**
 * Get formatted price string for display.
 */
export function formatPrice(plan: Plan, isYearly: boolean = false): string {
    const price = isYearly ? plan.price.yearly : plan.price.monthly;

    if (price === 0) {
        return 'Free forever';
    }

    return `$${price}/month`;
}

/**
 * Get early-bird price if available.
 */
export function getEarlyBirdPrice(plan: Plan, isYearly: boolean = false): number | null {
    if (!plan.earlyBirdPrice) return null;
    return isYearly ? plan.earlyBirdPrice.yearly : plan.earlyBirdPrice.monthly;
}
