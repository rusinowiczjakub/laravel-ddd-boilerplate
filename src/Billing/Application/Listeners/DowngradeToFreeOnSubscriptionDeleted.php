<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Listeners;

use Modules\Billing\Domain\Enums\Plan;
use Modules\Billing\Domain\Events\SubscriptionDeleted;
use Modules\Billing\Domain\Repositories\BillingWorkspaceRepository;
use Modules\Core\Attributes\Subscribe;
use Modules\Shared\Domain\ValueObjects\Id;

/**
 * DowngradeToFreeOnSubscriptionDeleted - Downgrades workspace to free plan when subscription ends.
 *
 * This listener handles customer.subscription.deleted webhook, which is triggered
 * when subscription period ends after cancellation or downgrade.
 */
#[Subscribe(SubscriptionDeleted::class)]
final readonly class DowngradeToFreeOnSubscriptionDeleted
{
    public function __construct(
        private BillingWorkspaceRepository $workspaceRepository,
    ) {}

    public function __invoke(SubscriptionDeleted $event): void
    {
        $workspace = $this->workspaceRepository->findById(new Id($event->workspaceId));

        if (!$workspace) {
            \Log::warning('Workspace not found for downgrade to free', [
                'workspace_id' => $event->workspaceId,
                'subscription_id' => $event->subscriptionId,
            ]);
            return;
        }

        // Downgrade to free plan and clear pending changes
        $workspace->changePlan(Plan::FREE);
        $workspace->clearPendingPlanChange();

        $this->workspaceRepository->save($workspace);

        \Log::info('Workspace downgraded to free plan after subscription ended', [
            'workspace_id' => $workspace->id()->value(),
            'subscription_id' => $event->subscriptionId,
        ]);
    }
}
