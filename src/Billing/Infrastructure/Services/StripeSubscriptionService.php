<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Services;

use DateTimeImmutable;
use DateTimeInterface;
use Modules\Billing\Domain\Enums\Plan;
use Modules\Billing\Domain\Services\SubscriptionService;
use Modules\Billing\Infrastructure\Models\WorkspaceModel;
use Modules\Shared\Domain\Exceptions\DomainException;
use Modules\Shared\Domain\ValueObjects\Uuid;

final readonly class StripeSubscriptionService implements SubscriptionService
{
    public function cancelSubscription(Uuid $workspaceId): DateTimeInterface
    {
        $workspace = WorkspaceModel::find($workspaceId->value());

        if (!$workspace) {
            throw new DomainException('Workspace not found');
        }

        if (!$workspace->stripe_id) {
            throw new DomainException('Workspace has no Stripe customer');
        }

        $subscription = $workspace->subscription('default');

        if (!$subscription || !$subscription->stripe_id) {
            throw new DomainException('No active subscription');
        }

        // Use Stripe API directly to cancel at period end
        $stripe = new \Stripe\StripeClient(config('cashier.secret'));

        $stripeSubscription = $stripe->subscriptions->update($subscription->stripe_id, [
            'cancel_at_period_end' => true,
        ]);

        // Get end date from Stripe subscription
        $periodEnd = $stripeSubscription->current_period_end ?? $stripeSubscription->cancel_at;

        if (!$periodEnd) {
            throw new DomainException('Cannot determine subscription end date');
        }

        // Update local subscription record
        $endsAt = (new DateTimeImmutable())->setTimestamp($periodEnd);
        $subscription->ends_at = $endsAt;
        $subscription->save();

        return $endsAt;
    }

    public function scheduleDowngrade(Uuid $workspaceId, string $newPlan, string $billingPeriod): DateTimeInterface
    {
        $workspace = WorkspaceModel::find($workspaceId->value());

        if (!$workspace) {
            throw new DomainException('Workspace not found');
        }

        if (!$workspace->subscribed('default')) {
            throw new DomainException('No active subscription');
        }

        $subscription = $workspace->subscription('default');
        $plan = Plan::from($newPlan);
        $newPriceId = $plan->stripePriceId($billingPeriod);

        if (!$newPriceId) {
            throw new DomainException("Price not configured for plan: {$plan->value}");
        }

        // Use Stripe API directly to schedule plan change at period end
        $stripe = new \Stripe\StripeClient(config('cashier.secret'));

        $stripe->subscriptions->update($subscription->stripe_id, [
            'items' => [
                [
                    'id' => $subscription->items()->first()->stripe_id,
                    'price' => $newPriceId,
                ],
            ],
            'proration_behavior' => 'none',
            'billing_cycle_anchor' => 'unchanged',
        ]);

        // Get the period end date
        $changesAt = $subscription->current_period_end;
        if (!$changesAt instanceof DateTimeInterface) {
            $changesAt = new DateTimeImmutable($changesAt);
        }

        return $changesAt;
    }
}
