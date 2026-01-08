<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Listeners;

use App\Services\WorkspaceCache;
use Modules\Billing\Domain\Events\PlanDowngradeScheduled;
use Modules\Billing\Domain\Events\SubscriptionCancellationRequested;
use Modules\Billing\Domain\Events\SubscriptionCreated;
use Modules\Billing\Domain\Events\SubscriptionUpdated;
use Modules\Billing\Domain\Events\SubscriptionDeleted;
use Modules\Core\Attributes\Subscribe;
use Modules\Core\Events\DomainEvent;

#[Subscribe([
    SubscriptionUpdated::class,
    SubscriptionDeleted::class,
    SubscriptionCreated::class,
    PlanDowngradeScheduled::class,
    SubscriptionCancellationRequested::class
])]
final readonly class InvalidateWorkspaceCacheOnSubscriptionChange
{
    public function __construct(
        private WorkspaceCache $cache,
    ) {}

    public function handle(
        DomainEvent $event
    ): void {
        $this->cache->invalidateWorkspaceSubscription($event->aggregateId());
    }
}
