<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Listeners;

use Modules\Billing\Domain\Events\SubscriptionUpdated;
use Modules\Core\Attributes\Subscribe;
use Illuminate\Support\Facades\DB;

/**
 * UpdateSubscriptionPeriodDatesOnUpdate - Updates subscription period dates on renewal/update.
 *
 * Handles customer.subscription.updated webhook to keep current_period_start/end in sync.
 */
#[Subscribe(SubscriptionUpdated::class)]
final readonly class UpdateSubscriptionPeriodDatesOnUpdate
{
    public function __invoke(SubscriptionUpdated $event): void
    {
        if (!$event->currentPeriodStart || !$event->currentPeriodEnd) {
            \Log::warning('Subscription updated without current_period dates', [
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

        \Log::info('Subscription period dates updated on renewal', [
            'subscription_id' => $event->subscriptionId,
            'current_period_end' => date('Y-m-d H:i:s', $event->currentPeriodEnd),
        ]);
    }
}
