<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Listeners;

use Modules\Billing\Domain\Events\PlanDowngradeScheduled;
use Modules\Billing\Domain\Repositories\BillingWorkspaceRepository;
use Modules\Billing\Domain\Services\SubscriptionService;
use Modules\Core\Attributes\Subscribe;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Shared\Domain\ValueObjects\Uuid;

#[Subscribe(PlanDowngradeScheduled::class)]
final readonly class HandlePlanDowngradeScheduled
{
    public function __construct(
        private SubscriptionService $subscriptionService,
        private BillingWorkspaceRepository $workspaceRepository,
    ) {}

    public function __invoke(PlanDowngradeScheduled $event): void
    {
        // Call infrastructure service to schedule downgrade in Stripe
        $changesAt = $this->subscriptionService->scheduleDowngrade(
            workspaceId: new Id($event->aggregateId()),
            newPlan: $event->newPlan,
            billingPeriod: $event->billingPeriod,
        );

        // Update domain model with pending plan change
        $workspace = $this->workspaceRepository->findById(new Id($event->aggregateId()));

        if (!$workspace) {
            return;
        }

        $workspace->setPendingPlanChange($event->newPlan, $event->billingPeriod, $changesAt);
        $this->workspaceRepository->save($workspace);
    }
}
