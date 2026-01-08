import { BillingPlanCard } from '@/components/settings/billing';
import SettingsLayout from '@/layouts/settings-layout';
import { type SharedData } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { getPlanById, formatPrice } from '@/config/plans';

export default function BillingSettings() {
    const { currentWorkspace, currentWorkspaceSubscription } = usePage<SharedData>().props;

    const planKey = currentWorkspace?.plan ?? 'free';
    const planConfig = getPlanById(planKey === 'free' ? null : planKey);

    const plan = {
        name: planConfig?.name ?? 'Free',
        price: planConfig ? formatPrice(planConfig) : 'Free forever',
        eventLimit: planConfig?.eventLimit ?? 1000,
    };

    const handleChangePlan = () => {
        // Redirect to change plan page
        router.get('/billing/change-plan');
    };

    const handleChangeEventLimits = () => {
        // This would typically open billing portal or change plan page
        if (planKey !== 'free') {
            router.post('/billing/portal');
        } else {
            router.get('/billing/change-plan');
        }
    };

    const handleOpenBillingPortal = () => {
        // Redirect to Stripe Customer Portal
        router.post('/billing/portal');
    };

    if (!currentWorkspace) {
        return (
            <SettingsLayout>
                <Head title="Billing Settings" />
                <div className="text-muted-foreground py-8 text-center">
                    No workspace selected. Please create or select a workspace first.
                </div>
            </SettingsLayout>
        );
    }

    return (
        <SettingsLayout>
            <Head title="Billing Settings" />

            <div className="space-y-6">
                <div>
                    <h1 className="text-3xl font-bold">Billing</h1>
                    <p className="text-muted-foreground">Manage your workspace's billing and subscription.</p>
                </div>

                <BillingPlanCard
                    planName={plan.name}
                    price={plan.price}
                    eventLimit={plan.eventLimit}
                    subscriptionStatus={currentWorkspaceSubscription?.subscriptionStatus}
                    subscriptionEndsAt={currentWorkspaceSubscription?.subscriptionEndsAt}
                    subscriptionCurrentPeriodEnd={currentWorkspaceSubscription?.subscriptionCurrentPeriodEnd}
                    pendingPlan={currentWorkspaceSubscription?.pendingPlan}
                    pendingBillingPeriod={currentWorkspaceSubscription?.pendingBillingPeriod}
                    planChangesAt={currentWorkspaceSubscription?.planChangesAt}
                    onChangePlan={handleChangePlan}
                    onChangeEventLimits={handleChangeEventLimits}
                    onOpenBillingPortal={handleOpenBillingPortal}
                />
            </div>
        </SettingsLayout>
    );
}
