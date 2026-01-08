<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Listeners;

use Modules\Billing\Domain\Events\SubscriptionCancellationRequested;
use Modules\Billing\Domain\Repositories\BillingWorkspaceRepository;
use Modules\Billing\Domain\Services\SubscriptionService;
use Modules\Core\Attributes\Subscribe;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Shared\Domain\ValueObjects\Uuid;

#[Subscribe(SubscriptionCancellationRequested::class)]
final readonly class HandleSubscriptionCancellationRequested
{
    public function __construct(
        private SubscriptionService $subscriptionService,
        private BillingWorkspaceRepository $workspaceRepository,
    ) {}


    public function __invoke(SubscriptionCancellationRequested $event): void
    {
        // Call infrastructure service to actually cancel in Stripe
        $endsAt = $this->subscriptionService->cancelSubscription(
            new Id($event->aggregateId())
        );

        // Update domain model with pending plan change
        $workspace = $this->workspaceRepository->findById(new Id($event->aggregateId()));

        if (!$workspace) {
            return;
        }

        $workspace->setPendingPlanChange('free', null, $endsAt);
        $this->workspaceRepository->save($workspace);
    }
}
