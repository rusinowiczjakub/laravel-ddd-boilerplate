<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Listeners;

use Modules\Billing\Application\Commands\ActivatePlanCommand;
use Modules\Billing\Domain\Events\SubscriptionCreated;
use Modules\Core\Attributes\Subscribe;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Shared\Domain\ValueObjects\Id;

/**
 * ActivatePlanOnSubscriptionCreated - Activates workspace plan when Stripe confirms subscription.
 *
 * This listener ensures plan activation is event-driven, happening only after
 * Stripe webhook confirmation (not synchronously during checkout).
 */
#[Subscribe(SubscriptionCreated::class)]
final readonly class ActivatePlanOnSubscriptionCreated
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(SubscriptionCreated $event): void
    {
        // Only activate if subscription is active
        if ($event->status !== 'active') {
            \Log::info('Subscription not active yet, skipping plan activation', [
                'workspace_id' => $event->workspaceId,
                'status' => $event->status,
            ]);

            return;
        }

        // Dispatch command to activate plan
        $this->commandBus->dispatch(new ActivatePlanCommand(
            workspaceId: Id::fromString($event->workspaceId),
            plan: $event->plan,
            subscriptionId: $event->subscriptionId,
        ));
    }
}
