import {Button} from '@/components/ui/button';
import {Card, CardContent, CardHeader, CardTitle} from '@/components/ui/card';
import {Label} from '@/components/ui/label';
import {Dot, ExternalLink, Info} from 'lucide-react';
import {SubscriptionStatusBadge} from '@/components/subscription-status-badge';
import {getPlanById} from '@/config/plans';

interface BillingPlanCardProps {
    planName: string;
    price: string;
    renewalDate?: string;
    eventLimit: number;
    additionalEvents?: string;
    subscriptionStatus?: 'active' | 'trialing' | 'past_due' | 'canceled' | null;
    subscriptionEndsAt?: string | null;
    subscriptionCurrentPeriodEnd?: string | null;
    pendingPlan?: string | null;
    pendingBillingPeriod?: string | null;
    planChangesAt?: string | null;
    onChangePlan: () => void;
    onChangeEventLimits: () => void;
    onOpenBillingPortal: () => void;
}

const formatNumber = (num: number): string => {
    if (num === -1 || num === Number.MAX_SAFE_INTEGER) return 'Unlimited';
    return num.toLocaleString();
};

const formatNextBillingDate = (dateString: string | null | undefined): string | null => {
    if (!dateString) return null;
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'});
};

export const BillingPlanCard = ({
                                    planName,
                                    price,
                                    renewalDate,
                                    eventLimit,
                                    additionalEvents = 'None',
                                    subscriptionStatus,
                                    subscriptionEndsAt,
                                    subscriptionCurrentPeriodEnd,
                                    pendingPlan,
                                    pendingBillingPeriod,
                                    planChangesAt,
                                    onChangePlan,
                                    onChangeEventLimits,
                                    onOpenBillingPortal,
                                }: BillingPlanCardProps) => {
    const plan = planName.toLowerCase();
    const nextBillingDate = formatNextBillingDate(subscriptionCurrentPeriodEnd);
    const changesAtDate = formatNextBillingDate(planChangesAt);

    // Get pending plan details
    const pendingPlanConfig = pendingPlan ? getPlanById(pendingPlan === 'free' ? null : pendingPlan) : null;
    const pendingPlanName = pendingPlanConfig?.name ?? 'Free';
    const pendingPlanPrice = pendingPlanConfig
        ? (pendingBillingPeriod === 'yearly' ? pendingPlanConfig.price.yearly : pendingPlanConfig.price.monthly)
        : 0;

    return (
        <Card>
            <CardHeader>
                <CardTitle>Subscription</CardTitle>
            </CardHeader>
            <CardContent className="space-y-6">
                <div className="flex items-start justify-between gap-4 border-b pb-6">
                    <div className="flex-1 space-y-2">
                        <Label>Plan</Label>
                        <div className="flex items-center gap-2">
                            <p className="text-muted-foreground text-sm">{planName}</p>
                            <SubscriptionStatusBadge
                                status={subscriptionStatus}
                                plan={plan}
                                endsAt={subscriptionEndsAt}
                            />
                        </div>
                    </div>
                    <div className="flex items-center gap-4">
                        <div className="text-right">
                            <div className="text-sm ">
                                <div>{price}</div>
                                {subscriptionStatus === 'active' && nextBillingDate && (
                                    <p className={'text-muted-foreground'}>Next billing: {nextBillingDate}</p>
                                )}
                            </div>
                        </div>
                        <Button variant="outline" size="sm" onClick={onChangePlan}>
                            Change
                        </Button>
                    </div>
                </div>

                {/* Pending Plan Change Banner */}
                {pendingPlan && changesAtDate && (
                    <div className="rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-950">
                        <div className="flex items-start gap-3">
                            <Info className="h-5 w-5 shrink-0 text-yellow-600 dark:text-yellow-400" />
                            <div className="flex-1">
                                <p className="font-medium text-yellow-900 dark:text-yellow-100">
                                    Plan change scheduled
                                </p>
                                <p className="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                    Your plan will change to <strong>{pendingPlanName}</strong>
                                    {pendingPlanPrice > 0 && (
                                        <> (${pendingPlanPrice}/month)</>
                                    )}
                                    {pendingPlanPrice === 0 && (
                                        <> (Free)</>
                                    )}
                                    {' '}on <strong>{changesAtDate}</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                {/*<div className="flex items-start justify-between gap-4 border-b pb-6">*/}
                {/*    <div className="flex-1">*/}
                {/*        <Label>Monthly event limits</Label>*/}
                {/*        <p className="text-muted-foreground text-sm">*/}
                {/*            Allow additional events beyond your included quota*/}
                {/*        </p>*/}
                {/*    </div>*/}
                {/*    <div className="flex items-center gap-4">*/}
                {/*        <div className="space-y-1 text-right text-sm">*/}
                {/*            <div className="flex gap-4">*/}
                {/*                <span className="text-muted-foreground">Plan inclusion</span>*/}
                {/*                <span className="font-medium">{formatNumber(eventLimit)}</span>*/}
                {/*            </div>*/}
                {/*            <div className="flex gap-4">*/}
                {/*                <span className="text-muted-foreground">Additional events</span>*/}
                {/*                <span className="font-medium">{additionalEvents}</span>*/}
                {/*            </div>*/}
                {/*        </div>*/}
                {/*        <Button variant="outline" size="sm" onClick={onChangeEventLimits}>*/}
                {/*            Change*/}
                {/*        </Button>*/}
                {/*    </div>*/}
                {/*</div>*/}

                <div className="flex items-center justify-between gap-4">
                    <div className="flex-1">
                        <Label>Billing portal</Label>
                        <p className="text-muted-foreground text-sm">
                            Manage payment methods, view invoices, and update billing details
                        </p>
                    </div>
                    <Button variant="outline" size="sm" onClick={onOpenBillingPortal}>
                        Go to billing portal
                        <ExternalLink className="ml-2 size-3"/>
                    </Button>
                </div>
            </CardContent>
        </Card>
    );
};
