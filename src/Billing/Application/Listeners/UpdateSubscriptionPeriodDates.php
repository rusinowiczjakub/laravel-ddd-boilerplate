<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Listeners;

use Modules\Billing\Domain\Events\SubscriptionCreated;
use Modules\Core\Attributes\Subscribe;
use Illuminate\Support\Facades\DB;

/**
 * UpdateSubscriptionPeriodDates - Updates subscription current_period_start and current_period_end.
 *
 * Cashier doesn't automatically populate these fields, so we do it manually
 * from the Stripe webhook data.
 */
#[Subscribe(SubscriptionCreated::class)]
final readonly class UpdateSubscriptionPeriodDates
{
    public function __invoke(SubscriptionCreated $event): void
    {
        if (!$event->currentPeriodStart || !$event->currentPeriodEnd) {
            \Log::warning('Subscription created without current_period dates', [
                'subscription_id' => $event->subscriptionId,
            ]);
            return;
        }

        DB::table('subscriptions')
            ->where('stripe_id', $event->subscriptionId)
            ->update([
                'current_period_start' => date('Y-m-d H:i:s', $event->currentPeriodStart),
                'current_period_end' => date('Y-m-d H:i:s', $event->currentPeriodEnd),
            ]);

        \Log::info('Subscription period dates updated', [
            'subscription_id' => $event->subscriptionId,
            'current_period_end' => date('Y-m-d H:i:s', $event->currentPeriodEnd),
        ]);
    }
}
